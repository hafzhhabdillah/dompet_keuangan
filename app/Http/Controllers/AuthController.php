<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Tampilkan form login.
     */
    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check()) {
            return Auth::user()->isAdmin()
                ? redirect()->route('admin.users.index')
                : redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    /**
     * Proses autentikasi login.
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if ($user && ! Hash::check($credentials['password'], $user->password)) {
            return back()->withInput($request->only('email'))->withErrors([
                'email' => 'Email atau password yang Anda masukkan salah.',
            ]);
        }

        if ($user && $user->status === 'nonaktif') {
            return back()->withInput($request->only('email'))->withErrors([
                'email' => 'Akun Anda dinonaktifkan oleh Administrator. Silakan hubungi pengelola sistem.',
            ]);
        }

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            if (Auth::user()->isAdmin()) {
                return redirect()->intended(route('admin.users.index'))
                    ->with('success', 'Selamat datang kembali, Administrator '.Auth::user()->nama.'!');
            }

            return redirect()->intended(route('dashboard'))
                ->with('success', 'Selamat datang kembali, '.Auth::user()->nama.'!');
        }

        return back()->withInput($request->only('email'))->withErrors([
            'email' => 'Email atau password yang Anda masukkan salah.',
        ]);
    }

    /**
     * Tampilkan form register.
     */
    public function showRegister(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.register');
    }

    /**
     * Proses pendaftaran user baru (role default = user, status = aktif).
     */
    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'nama.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah terdaftar. Silakan gunakan email lain atau login.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal terdiri dari 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $user = User::create([
            'nama' => $validated['nama'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'user',
            'status' => 'aktif',
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard')->with('success', 'Pendaftaran berhasil! Selamat datang di aplikasi Pencatatan Keuangan.');
    }

    /**
     * Logout pengguna.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah berhasil logout.');
    }
}
