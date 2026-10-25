<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TopUpController extends Controller
{
    /**
     * Tampilkan form pengisian saldo.
     */
    public function index(): View
    {
        /** @var User $user */
        $user = Auth::user();

        return view('transactions.topup', [
            'balance' => $user->getBalance(),
        ]);
    }

    /**
     * Proses pengisian saldo.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'jumlah' => ['required', 'numeric', 'min:1000'],
            'tanggal' => ['required', 'date'],
            'keterangan' => ['nullable', 'string', 'max:255'],
        ], [
            'jumlah.required' => 'Jumlah saldo yang akan diisi wajib ditentukan.',
            'jumlah.numeric' => 'Jumlah saldo harus berupa nominal angka valid.',
            'jumlah.min' => 'Jumlah pengisian saldo minimal Rp 1.000.',
            'tanggal.required' => 'Tanggal pengisian wajib dipilih.',
            'tanggal.date' => 'Format tanggal tidak valid.',
        ]);

        // Temukan kategori 'Isi Saldo' (kategori bawaan atau buat jika belum ada)
        $category = Category::where('nama', 'Isi Saldo')
            ->where('tipe', 'pemasukan')
            ->whereNull('user_id')
            ->first();

        if (! $category) {
            $category = Category::create([
                'nama' => 'Isi Saldo',
                'tipe' => 'pemasukan',
                'user_id' => null,
            ]);
        }

        Transaction::create([
            'user_id' => Auth::id(),
            'category_id' => $category->id,
            'tipe' => 'pemasukan',
            'jumlah' => $validated['jumlah'],
            'tanggal' => $validated['tanggal'] ?? Carbon::today()->format('Y-m-d'),
            'keterangan' => $validated['keterangan'] ?? 'Pengisian Saldo Dompet Keuangan',
        ]);

        return redirect()->route('dashboard')->with('success', 'Saldo sebesar Rp '.number_format($validated['jumlah'], 0, ',', '.').' berhasil ditambahkan ke akun Anda!');
    }
}
