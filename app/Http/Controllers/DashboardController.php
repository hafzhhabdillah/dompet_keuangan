<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Tampilkan halaman dashboard utama.
     */
    public function index(): View|RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        if ($user->isAdmin()) {
            return redirect()->route('admin.users.index');
        }

        $totalIncome = $user->getTotalIncome();
        $totalExpense = $user->getTotalExpense();
        $balance = $totalIncome - $totalExpense;

        // Ambil 5 transaksi terbaru milik user
        $recentTransactions = $user->transactions()
            ->with('category')
            ->orderBy('tanggal', 'desc')
            ->orderBy('id', 'desc')
            ->take(5)
            ->get();

        // Kategori pengeluaran teratas untuk insight
        $topExpenses = $user->transactions()
            ->where('tipe', 'pengeluaran')
            ->with('category')
            ->selectRaw('category_id, SUM(jumlah) as total')
            ->groupBy('category_id')
            ->orderByDesc('total')
            ->take(4)
            ->get();

        return view('dashboard', [
            'totalIncome' => $totalIncome,
            'totalExpense' => $totalExpense,
            'balance' => $balance,
            'recentTransactions' => $recentTransactions,
            'topExpenses' => $topExpenses,
            'user' => $user,
        ]);
    }
}
