<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Tampilkan halaman profil pengguna.
     */
    public function index(): View
    {
        return view('profile.index', [
            'user' => Auth::user(),
        ]);
    }

    /**
     * Perbarui data profil (nama, email, password).
     */
    public function update(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'current_password' => ['nullable', 'required_with:new_password'],
            'new_password' => ['nullable', 'string', 'min:6', 'confirmed'],
        ], [
            'nama.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan oleh akun lain.',
            'current_password.required_with' => 'Password saat ini harus diisi jika ingin mengganti password.',
            'new_password.min' => 'Password baru minimal 6 karakter.',
            'new_password.confirmed' => 'Konfirmasi password baru tidak cocok.',
        ]);

        // Cek current password jika ada permintaan ganti password
        if (! empty($validated['current_password'])) {
            if (! Hash::check($validated['current_password'], $user->password)) {
                return back()->withErrors([
                    'current_password' => 'Password saat ini yang Anda masukkan salah.',
                ]);
            }

            $user->password = Hash::make($validated['new_password']);
        }

        $user->nama = $validated['nama'];
        $user->email = $validated['email'];
        $user->save();

        return redirect()->route('profile.index')->with('success', 'Profil Anda berhasil diperbarui.');
    }
}
