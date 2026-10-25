@extends('layouts.app', ['title' => 'Daftar Akun Baru'])

@section('content')
<div style="max-width: 480px; margin: 1.5rem auto;">
    <div class="card" style="padding: 2.25rem;">
        <div style="text-align: center; margin-bottom: 2rem;">
            <div style="width: 54px; height: 54px; background: #ecfdf5; color: #059669; border-radius: 14px; display: inline-flex; align-items: center; justify-content: center; font-size: 1.5rem; margin-bottom: 0.75rem;">
                <i class="fa-solid fa-user-plus"></i>
            </div>
            <h1 style="font-size: 1.5rem; font-weight: 800; color: #0f172a; margin-bottom: 0.35rem;">Buat Akun Baru</h1>
            <p style="color: #64748b; font-size: 0.925rem;">Mulai catat dan rencanakan finansial Anda hari ini</p>
        </div>

        <form action="{{ route('register') }}" method="POST">
            @csrf
            
            <div class="form-group">
                <label for="nama" class="form-label">Nama Lengkap</label>
                <input type="text" id="nama" name="nama" class="form-control" value="{{ old('nama') }}" required autofocus placeholder="Contoh: Budi Santoso">
                @error('nama')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="email" class="form-label">Alamat Email</label>
                <input type="email" id="email" name="email" class="form-control" value="{{ old('email') }}" required placeholder="nama@email.com">
                @error('email')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <input type="password" id="password" name="password" class="form-control" required placeholder="Minimal 6 karakter">
                @error('password')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="password_confirmation" class="form-label">Ulangi Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required placeholder="Ketik ulang password">
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.8rem; font-size: 1rem; margin-top: 0.75rem;">
                <i class="fa-solid fa-check"></i> Selesaikan Pendaftaran
            </button>
        </form>

        <div style="text-align: center; margin-top: 1.75rem; padding-top: 1.25rem; border-top: 1px solid #f1f5f9; font-size: 0.925rem; color: #64748b;">
            Sudah memiliki akun? <a href="{{ route('login') }}" style="color: var(--primary); font-weight: 700; text-decoration: none;">Masuk di sini</a>
        </div>
    </div>
</div>
@endsection
