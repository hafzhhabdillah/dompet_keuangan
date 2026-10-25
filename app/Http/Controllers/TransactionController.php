<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TransactionController extends Controller
{
    /**
     * Tampilkan riwayat transaksi dengan filter jenis & kategori.
     */
    public function index(Request $request): View
    {
        /** @var User $user */
        $user = Auth::user();

        $query = $user->transactions()->with('category');

        // Filter tipe (pemasukan / pengeluaran)
        if ($request->filled('tipe')) {
            $query->where('tipe', $request->tipe);
        }

        // Filter kategori
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter tanggal mulai
        if ($request->filled('start_date')) {
            $query->whereDate('tanggal', '>=', $request->start_date);
        }

        // Filter tanggal selesai
        if ($request->filled('end_date')) {
            $query->whereDate('tanggal', '<=', $request->end_date);
        }

        $transactions = $query->orderBy('tanggal', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(15)
            ->withQueryString();

        // Kategori yang tersedia untuk filter
        $categories = Category::availableForUser($user->id)
            ->orderBy('tipe')
            ->orderBy('nama')
            ->get();

        return view('transactions.index', [
            'transactions' => $transactions,
            'categories' => $categories,
            'currentBalance' => $user->getBalance(),
            'totalIncome' => $user->getTotalIncome(),
            'totalExpense' => $user->getTotalExpense(),
        ]);
    }

    /**
     * Tampilkan form pembuatan catatan transaksi.
     */
    public function create(Request $request): View
    {
        /** @var User $user */
        $user = Auth::user();

        // Tipe default dari query string jika ada (?tipe=pemasukan atau ?tipe=pengeluaran)
        $selectedTipe = in_array($request->query('tipe'), ['pemasukan', 'pengeluaran'])
            ? $request->query('tipe')
            : 'pengeluaran';

        // Ambil kategori bawaan & kategori user
        $categories = Category::availableForUser($user->id)
            ->orderBy('nama')
            ->get();

        return view('transactions.create', [
            'selectedTipe' => $selectedTipe,
            'categories' => $categories,
            'currentBalance' => $user->getBalance(),
        ]);
    }

    /**
     * Simpan catatan transaksi baru.
     */
    public function store(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        $validated = $request->validate([
            'tipe' => ['required', 'in:pemasukan,pengeluaran'],
            'category_id' => [
                'required',
                'exists:categories,id',
            ],
            'jumlah' => ['required', 'numeric', 'min:1'],
            'tanggal' => ['required', 'date'],
            'keterangan' => ['nullable', 'string', 'max:500'],
        ], [
            'tipe.required' => 'Jenis transaksi wajib dipilih.',
            'category_id.required' => 'Kategori transaksi wajib dipilih.',
            'category_id.exists' => 'Kategori yang dipilih tidak valid.',
            'jumlah.required' => 'Nominal transaksi wajib diisi.',
            'jumlah.numeric' => 'Nominal transaksi harus berupa angka.',
            'jumlah.min' => 'Nominal transaksi minimal Rp 1.',
            'tanggal.required' => 'Tanggal transaksi wajib diisi.',
            'tanggal.date' => 'Format tanggal tidak valid.',
        ]);

        // Verifikasi kepemilikan kategori: harus bawaan (user_id null) atau milik user login
        $category = Category::where('id', $validated['category_id'])
            ->where(function ($q) use ($user) {
                $q->whereNull('user_id')->orWhere('user_id', $user->id);
            })
            ->first();

        if (! $category) {
            return back()->withInput()->withErrors([
                'category_id' => 'Kategori yang Anda pilih tidak tersedia atau bukan milik Anda.',
            ]);
        }

        // Pastikan tipe kategori cocok dengan tipe transaksi
        if ($category->tipe !== $validated['tipe']) {
            return back()->withInput()->withErrors([
                'category_id' => 'Kategori "'.$category->nama.'" bertipe '.$category->tipe.', tidak cocok dengan jenis transaksi '.$validated['tipe'].'.',
            ]);
        }

        // Cegah pengeluaran yang membuat saldo menjadi negatif
        $currentBalance = $user->getBalance();
        if ($validated['tipe'] === 'pengeluaran' && $validated['jumlah'] > $currentBalance) {
            return back()->withInput()->withErrors([
                'jumlah' => 'Saldo Anda tidak mencukupi untuk melakukan pengeluaran sebesar Rp '.number_format($validated['jumlah'], 0, ',', '.').'. Saldo saat ini hanya Rp '.number_format($currentBalance, 0, ',', '.').'.',
            ]);
        }

        $user->transactions()->create([
            'category_id' => $validated['category_id'],
            'tipe' => $validated['tipe'],
            'jumlah' => $validated['jumlah'],
            'tanggal' => $validated['tanggal'],
            'keterangan' => $validated['keterangan'],
        ]);

        $label = $validated['tipe'] === 'pemasukan' ? 'Pemasukan' : 'Pengeluaran';

        return redirect()->route('transactions.index')->with('success', 'Catatan '.$label.' berhasil ditambahkan.');
    }

    /**
     * Tampilkan form edit transaksi.
     */
    public function edit(Transaction $transaction): View
    {
        // Validasi kepemilikan data di sisi server
        if ($transaction->user_id !== Auth::id()) {
            abort(403, 'Akses Ditolak: Anda tidak memiliki izin untuk mengedit transaksi milik pengguna lain.');
        }

        $categories = Category::availableForUser(Auth::id())
            ->orderBy('nama')
            ->get();

        return view('transactions.edit', [
            'transaction' => $transaction,
            'categories' => $categories,
            'currentBalance' => Auth::user()->getBalance(),
        ]);
    }

    /**
     * Perbarui data transaksi yang ada.
     */
    public function update(Request $request, Transaction $transaction): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        // Validasi kepemilikan data di sisi server
        if ($transaction->user_id !== $user->id) {
            abort(403, 'Akses Ditolak: Anda tidak memiliki izin untuk memperbarui transaksi milik pengguna lain.');
        }

        $validated = $request->validate([
            'tipe' => ['required', 'in:pemasukan,pengeluaran'],
            'category_id' => ['required', 'exists:categories,id'],
            'jumlah' => ['required', 'numeric', 'min:1'],
            'tanggal' => ['required', 'date'],
            'keterangan' => ['nullable', 'string', 'max:500'],
        ], [
            'tipe.required' => 'Jenis transaksi wajib dipilih.',
            'category_id.required' => 'Kategori transaksi wajib dipilih.',
            'category_id.exists' => 'Kategori yang dipilih tidak valid.',
            'jumlah.required' => 'Nominal transaksi wajib diisi.',
            'jumlah.numeric' => 'Nominal transaksi harus berupa angka.',
            'jumlah.min' => 'Nominal transaksi minimal Rp 1.',
            'tanggal.required' => 'Tanggal transaksi wajib diisi.',
        ]);

        // Verifikasi kategori tersedia untuk user
        $category = Category::where('id', $validated['category_id'])
            ->where(function ($q) use ($user) {
                $q->whereNull('user_id')->orWhere('user_id', $user->id);
            })
            ->first();

        if (! $category) {
            return back()->withInput()->withErrors([
                'category_id' => 'Kategori yang dipilih tidak valid atau bukan milik Anda.',
            ]);
        }

        if ($category->tipe !== $validated['tipe']) {
            return back()->withInput()->withErrors([
                'category_id' => 'Kategori "'.$category->nama.'" bertipe '.$category->tipe.', tidak cocok dengan jenis transaksi '.$validated['tipe'].'.',
            ]);
        }

        // Simulasi saldo baru setelah pengeditan untuk mencegah saldo negatif
        $currentIncome = $user->getTotalIncome();
        $currentExpense = $user->getTotalExpense();

        $virtualIncome = $currentIncome - ($transaction->tipe === 'pemasukan' ? $transaction->jumlah : 0) + ($validated['tipe'] === 'pemasukan' ? $validated['jumlah'] : 0);
        $virtualExpense = $currentExpense - ($transaction->tipe === 'pengeluaran' ? $transaction->jumlah : 0) + ($validated['tipe'] === 'pengeluaran' ? $validated['jumlah'] : 0);

        if (($virtualIncome - $virtualExpense) < 0) {
            return back()->withInput()->withErrors([
                'jumlah' => 'Perubahan transaksi ini ditolak karena akan menyebabkan total saldo Anda menjadi negatif (Rp '.number_format($virtualIncome - $virtualExpense, 0, ',', '.').').',
            ]);
        }

        $transaction->update([
            'category_id' => $validated['category_id'],
            'tipe' => $validated['tipe'],
            'jumlah' => $validated['jumlah'],
            'tanggal' => $validated['tanggal'],
            'keterangan' => $validated['keterangan'],
        ]);

        return redirect()->route('transactions.index')->with('success', 'Catatan transaksi berhasil diperbarui.');
    }

    /**
     * Hapus transaksi keuangan.
     */
    public function destroy(Transaction $transaction): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        // Validasi kepemilikan data di sisi server
        if ($transaction->user_id !== $user->id) {
            abort(403, 'Akses Ditolak: Anda tidak memiliki izin untuk menghapus transaksi milik pengguna lain.');
        }

        // Jika transaksi yang dihapus adalah pemasukan, pastikan sisa saldo tidak menjadi negatif
        if ($transaction->tipe === 'pemasukan') {
            $balanceAfterDelete = $user->getBalance() - $transaction->jumlah;
            if ($balanceAfterDelete < 0) {
                return back()->with('error', 'Transaksi pemasukan ini tidak dapat dihapus karena saldo Anda akan menjadi negatif (Rp '.number_format($balanceAfterDelete, 0, ',', '.').').');
            }
        }

        $transaction->delete();

        return redirect()->route('transactions.index')->with('success', 'Catatan transaksi berhasil dihapus.');
    }
}
