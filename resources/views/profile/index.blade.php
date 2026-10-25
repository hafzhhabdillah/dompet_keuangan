@extends('layouts.app', ['title' => 'Profil Saya'])

@section('content')
<div style="max-width: 700px; margin: 0 auto;">
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem;">
        <div>
            <h1 style="font-size: 1.5rem; font-weight: 800; color: #0f172a;">Pengaturan Profil</h1>
            <p style="color: #64748b; font-size: 0.925rem;">Lihat dan perbarui informasi akun serta keamanan kata sandi Anda</p>
        </div>
    </div>

    <!-- Status Card -->
    <div class="card" style="padding: 1.5rem; background: linear-gradient(135deg, #f8fafc, #edf2f7); margin-bottom: 1.5rem;">
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
            <div style="display: flex; align-items: center; gap: 1rem;">
                <div style="width: 50px; height: 50px; background: var(--primary); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; font-weight: 700;">
                    {{ strtoupper(substr($user->nama, 0, 1)) }}
                </div>
                <div>
                    <h2 style="font-size: 1.15rem; font-weight: 700; color: #0f172a;">{{ $user->nama }}</h2>
                    <div style="color: #64748b; font-size: 0.875rem;">{{ $user->email }}</div>
                </div>
            </div>
            <div style="display: flex; gap: 0.5rem; align-items: center;">
                <span class="badge-role {{ $user->role }}">{{ ucfirst($user->role) }}</span>
                <span class="badge {{ $user->status === 'aktif' ? 'badge-active' : 'badge-inactive' }}">
                    <i class="fa-solid fa-circle" style="font-size: 0.5rem;"></i> {{ ucfirst($user->status) }}
                </span>
            </div>
        </div>
        <div style="margin-top: 1rem; padding-top: 0.75rem; border-top: 1px solid #e2e8f0; font-size: 0.825rem; color: #64748b;">
            <i class="fa-regular fa-calendar-check"></i> Terdaftar sejak: {{ $user->created_at->translatedFormat('d F Y') }}
        </div>
    </div>

    <!-- Form Update Profile -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">
                <i class="fa-solid fa-user-pen" style="color: var(--primary);"></i> Ubah Data Diri & Keamanan
            </h2>
        </div>

        <form action="{{ route('profile.update') }}" method="POST">
            @csrf
            @method('PUT')

            <h3 style="font-size: 1rem; font-weight: 700; color: #334155; margin-bottom: 1rem;">Informasi Dasar</h3>

            <div class="form-group">
                <label for="nama" class="form-label">Nama Lengkap</label>
                <input type="text" id="nama" name="nama" class="form-control" value="{{ old('nama', $user->nama) }}" required>
                @error('nama')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="email" class="form-label">Alamat Email</label>
                <input type="email" id="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                @error('email')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div style="margin: 2rem 0 1.25rem 0; padding-top: 1.25rem; border-top: 1px dashed #cbd5e1;">
                <h3 style="font-size: 1rem; font-weight: 700; color: #334155; margin-bottom: 0.35rem;">Ganti Kata Sandi</h3>
                <p style="font-size: 0.85rem; color: #64748b; margin-bottom: 1rem;">Kosongkan jika Anda tidak ingin mengubah password saat ini.</p>
            </div>

            <div class="form-group">
                <label for="current_password" class="form-label">Password Saat Ini</label>
                <input type="password" id="current_password" name="current_password" class="form-control" placeholder="Masukkan password saat ini untuk verifikasi">
                @error('current_password')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label for="new_password" class="form-label">Password Baru</label>
                    <input type="password" id="new_password" name="new_password" class="form-control" placeholder="Minimal 6 karakter">
                    @error('new_password')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="new_password_confirmation" class="form-label">Ulangi Password Baru</label>
                    <input type="password" id="new_password_confirmation" name="new_password_confirmation" class="form-control" placeholder="Konfirmasi password baru">
                </div>
            </div>

            <div style="margin-top: 1.5rem; display: flex; justify-content: flex-end;">
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan Profil
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
