@extends('layouts.app', ['title' => 'Edit Kategori Kustom'])

@section('content')
<div style="max-width: 550px; margin: 0 auto;">
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem;">
        <div>
            <h1 style="font-size: 1.5rem; font-weight: 800; color: #0f172a;">Edit Kategori Kustom</h1>
            <p style="color: #64748b; font-size: 0.925rem;">Perbarui kategori "{{ $category->nama }}"</p>
        </div>
        <a href="{{ route('categories.index') }}" class="btn btn-secondary btn-sm">
            <i class="fa-solid fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="card">
        <form action="{{ route('categories.update', $category) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="nama" class="form-label">Nama Kategori</label>
                <input type="text" id="nama" name="nama" class="form-control" value="{{ old('nama', $category->nama) }}" required autofocus>
                @error('nama')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="tipe" class="form-label">Tipe Kategori</label>
                <select name="tipe" id="tipe" class="form-control" required>
                    <option value="pengeluaran" {{ old('tipe', $category->tipe) === 'pengeluaran' ? 'selected' : '' }}>Pengeluaran</option>
                    <option value="pemasukan" {{ old('tipe', $category->tipe) === 'pemasukan' ? 'selected' : '' }}>Pemasukan</option>
                </select>
                @error('tipe')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div style="margin-top: 1.75rem; display: flex; justify-content: flex-end; gap: 0.75rem;">
                <a href="{{ route('categories.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
