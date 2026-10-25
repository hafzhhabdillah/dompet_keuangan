@extends('layouts.app', ['title' => 'Masuk ke Akun'])

@section('content')
<div style="max-width: 460px; margin: 1.5rem auto;">
    <div class="card" style="padding: 2.25rem;">
        <div style="text-align: center; margin-bottom: 2rem;">
            <div style="width: 54px; height: 54px; background: #ecfdf5; color: #059669; border-radius: 14px; display: inline-flex; align-items: center; justify-content: center; font-size: 1.5rem; margin-bottom: 0.75rem;">
                <i class="fa-solid fa-lock"></i>
            </div>
            <h1 style="font-size: 1.5rem; font-weight: 800; color: #0f172a; margin-bottom: 0.35rem;">Masuk ke Akun</h1>
            <p style="color: #64748b; font-size: 0.925rem;">Kelola dan pantau keuangan pribadi Anda secara rapi</p>
        </div>

        <form action="{{ route('login') }}" method="POST">
            @csrf
            
            <div class="form-group">
                <label for="email" class="form-label">Alamat Email</label>
                <div style="position: relative;">
                    <input type="email" id="email" name="email" class="form-control" value="{{ old('email') }}" required autofocus placeholder="nama@email.com">
                </div>
                @error('email')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.4rem;">
                    <label for="password" class="form-label" style="margin-bottom: 0;">Password</label>
                </div>
                <input type="password" id="password" name="password" class="form-control" required placeholder="••••••••">
                @error('password')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group" style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1.5rem;">
                <input type="checkbox" id="remember" name="remember" style="accent-color: var(--primary); width: 16px; height: 16px; cursor: pointer;">
                <label for="remember" style="font-size: 0.875rem; color: #475569; cursor: pointer; user-select: none;">Ingat saya di perangkat ini</label>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.8rem; font-size: 1rem;">
                <i class="fa-solid fa-right-to-bracket"></i> Masuk Sekarang
            </button>
        </form>

        <div style="text-align: center; margin-top: 1.75rem; padding-top: 1.25rem; border-top: 1px solid #f1f5f9; font-size: 0.925rem; color: #64748b;">
            Belum memiliki akun? <a href="{{ route('register') }}" style="color: var(--primary); font-weight: 700; text-decoration: none;">Daftar akun baru</a>
        </div>
    </div>

    <!-- Demo Accounts Helper Box -->
    <div class="card" style="background: #f8fafc; border: 1px dashed #cbd5e1; padding: 1.25rem;">
        <h2 style="font-size: 0.875rem; font-weight: 700; color: #334155; margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.5rem;">
            <i class="fa-solid fa-circle-info" style="color: #0284c7;"></i> Akun Demo Siap Pakai:
        </h2>
        <div style="font-size: 0.825rem; color: #475569; display: flex; flex-direction: column; gap: 0.6rem;">
            <div style="background: white; padding: 0.6rem 0.75rem; border-radius: 6px; border: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <strong style="color: #991b1b;">Admin:</strong> admin@keuangan.test
                </div>
                <button type="button" class="btn btn-secondary btn-sm" onclick="fillLogin('admin@keuangan.test', 'admin123')" style="padding: 0.2rem 0.5rem; font-size: 0.75rem;">Gunakan</button>
            </div>
            <div style="background: white; padding: 0.6rem 0.75rem; border-radius: 6px; border: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <strong style="color: #0369a1;">User:</strong> budi@keuangan.test
                </div>
                <button type="button" class="btn btn-secondary btn-sm" onclick="fillLogin('budi@keuangan.test', 'user123')" style="padding: 0.2rem 0.5rem; font-size: 0.75rem;">Gunakan</button>
            </div>
        </div>
    </div>
</div>

<script>
    function fillLogin(email, password) {
        document.getElementById('email').value = email;
        document.getElementById('password').value = password;
    }
</script>
@endsection
