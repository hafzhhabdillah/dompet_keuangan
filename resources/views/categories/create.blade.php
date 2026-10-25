@extends('layouts.app', ['title' => 'Buat Kategori Kustom'])

@section('content')
<div style="max-width: 550px; margin: 0 auto;">
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem;">
        <div>
            <h1 style="font-size: 1.5rem; font-weight: 800; color: #0f172a;">Kategori Kustom Baru</h1>
            <p style="color: #64748b; font-size: 0.925rem;">Buat pos kategori khusus untuk akun Anda</p>
        </div>
        <a href="{{ route('categories.index') }}" class="btn btn-secondary btn-sm">
            <i class="fa-solid fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="card">
        <form action="{{ route('categories.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="nama" class="form-label">Nama Kategori</label>
                <input type="text" id="nama" name="nama" class="form-control" value="{{ old('nama') }}" required autofocus placeholder="Contoh: Tabungan Liburan, Kopi Sore, dll">
                @error('nama')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="tipe" class="form-label">Tipe Kategori</label>
                <select name="tipe" id="tipe" class="form-control" required>
                    <option value="">-- Pilih Tipe --</option>
                    <option value="pengeluaran" {{ old('tipe', 'pengeluaran') === 'pengeluaran' ? 'selected' : '' }}>Pengeluaran (Uang Keluar)</option>
                    <option value="pemasukan" {{ old('tipe') === 'pemasukan' ? 'selected' : '' }}>Pemasukan (Uang Masuk)</option>
                </select>
                @error('tipe')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div style="margin-top: 1.75rem; display: flex; justify-content: flex-end; gap: 0.75rem;">
                <a href="{{ route('categories.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-check"></i> Simpan Kategori
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
