@extends('layouts.app', ['title' => '403 - Akses Ditolak'])

@section('content')
<div style="max-width: 550px; margin: 4rem auto; text-align: center;">
    <div class="card" style="padding: 3rem 2rem;">
        <div style="width: 80px; height: 80px; background: #fee2e2; color: #dc2626; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 2.5rem; margin-bottom: 1.5rem;">
            <i class="fa-solid fa-ban"></i>
        </div>
        <h1 style="font-size: 2rem; font-weight: 800; color: #0f172a; margin-bottom: 0.5rem;">403 - Akses Ditolak</h1>
        <p style="color: #64748b; font-size: 1rem; line-height: 1.6; margin-bottom: 2rem;">
            {{ $exception->getMessage() ?: 'Maaf, Anda tidak memiliki hak akses atau izin yang sesuai untuk membuka halaman ini.' }}
        </p>
        <div style="display: flex; justify-content: center; gap: 1rem;">
            @auth
                <a href="{{ Auth::user()->isAdmin() ? route('admin.users.index') : route('dashboard') }}" class="btn btn-primary">
                    <i class="fa-solid fa-house"></i> Kembali ke Halaman Utama
                </a>
            @else
                <a href="{{ route('login') }}" class="btn btn-primary">
                    <i class="fa-solid fa-right-to-bracket"></i> Masuk Akun
                </a>
            @endauth
        </div>
    </div>
</div>
@endsection
