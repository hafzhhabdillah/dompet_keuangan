<?php

use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TopUpController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Redirect halaman depan
Route::get('/', function () {
    if (Auth::check()) {
        return Auth::user()->isAdmin()
            ? redirect()->route('admin.users.index')
            : redirect()->route('dashboard');
    }

    return redirect()->route('login');
});

// Autentikasi Pengguna (Guest)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Route Khusus Setelah Login (Auth + Cek Status Akun Aktif)
Route::middleware(['auth', 'check.status'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Profil Pengguna (Bisa diakses Admin & User)
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Fitur Khusus User Biasa
    Route::middleware('role:user')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Isi Saldo (Top-up)
        Route::get('/topup', [TopUpController::class, 'index'])->name('topup.index');
        Route::post('/topup', [TopUpController::class, 'store'])->name('topup.store');

        // CRUD Catatan Transaksi Keuangan
        Route::resource('transactions', TransactionController::class);

        // CRUD Kategori Kustom
        Route::resource('categories', CategoryController::class);
    });

    // Fitur Khusus Admin
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
        Route::patch('/users/{user}/toggle-status', [AdminUserController::class, 'toggleStatus'])->name('users.toggle-status');
        Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');
    });
});
