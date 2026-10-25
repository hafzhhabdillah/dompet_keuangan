<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Tampilkan daftar seluruh pengguna.
     */
    public function index(Request $request): View
    {
        $query = User::withCount('transactions');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $users = $query->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        $totalUsers = User::count();
        $activeUsers = User::where('status', 'aktif')->count();
        $inactiveUsers = User::where('status', 'nonaktif')->count();

        return view('admin.users.index', [
            'users' => $users,
            'totalUsers' => $totalUsers,
            'activeUsers' => $activeUsers,
            'inactiveUsers' => $inactiveUsers,
        ]);
    }

    /**
     * Ubah status akun user (aktif <-> nonaktif).
     */
    public function toggleStatus(User $user): RedirectResponse
    {
        // Admin tidak boleh menonaktifkan akun sendiri
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Anda tidak dapat menonaktifkan akun administrator Anda sendiri!');
        }

        $newStatus = $user->status === 'aktif' ? 'nonaktif' : 'aktif';
        $user->status = $newStatus;
        $user->save();

        $statusLabel = $newStatus === 'aktif' ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "Akun pengguna {$user->nama} ({$user->email}) berhasil {$statusLabel}.");
    }

    /**
     * Hapus akun user beserta seluruh data transaksinya.
     */
    public function destroy(User $user): RedirectResponse
    {
        // Admin tidak boleh menghapus akun sendiri
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun administrator Anda sendiri!');
        }

        $userName = $user->nama;
        $userEmail = $user->email;
        $user->delete();

        return back()->with('success', "Akun pengguna {$userName} ({$userEmail}) dan seluruh datanya berhasil dihapus permanen.");
    }
}
