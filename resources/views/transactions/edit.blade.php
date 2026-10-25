@extends('layouts.app', ['title' => 'Edit Catatan Transaksi'])

@section('content')
<div style="max-width: 650px; margin: 0 auto;">
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem;">
        <div>
            <h1 style="font-size: 1.5rem; font-weight: 800; color: #0f172a;">Edit Catatan Transaksi</h1>
            <p style="color: #64748b; font-size: 0.925rem;">Perbarui rincian transaksi #{{ $transaction->id }}</p>
        </div>
        <a href="{{ route('transactions.index') }}" class="btn btn-secondary btn-sm">
            <i class="fa-solid fa-arrow-left"></i> Kembali
        </a>
    </div>

    <!-- Saldo Info Card -->
    <div class="card" style="padding: 1rem 1.25rem; background: #f8fafc; border-color: #cbd5e1; margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center;">
        <span style="font-size: 0.9rem; color: #475569; font-weight: 600;">Saldo Saat Ini:</span>
        <strong style="font-size: 1.15rem; color: #0f172a;">Rp {{ number_format($currentBalance, 0, ',', '.') }}</strong>
    </div>

    <div class="card">
        <form action="{{ route('transactions.update', $transaction) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Toggle Jenis Transaksi -->
            <div class="form-group">
                <label class="form-label">Jenis Transaksi</label>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <label style="display: flex; align-items: center; justify-content: center; gap: 0.5rem; padding: 0.85rem; border: 2px solid #cbd5e1; border-radius: 10px; cursor: pointer; font-weight: 700; transition: all 0.2s;" id="labelPemasukan">
                        <input type="radio" name="tipe" value="pemasukan" {{ old('tipe', $transaction->tipe) === 'pemasukan' ? 'checked' : '' }} onchange="switchTipe('pemasukan')" style="display: none;">
                        <i class="fa-solid fa-arrow-down" style="color: #10b981;"></i> Pemasukan
                    </label>

                    <label style="display: flex; align-items: center; justify-content: center; gap: 0.5rem; padding: 0.85rem; border: 2px solid #cbd5e1; border-radius: 10px; cursor: pointer; font-weight: 700; transition: all 0.2s;" id="labelPengeluaran">
                        <input type="radio" name="tipe" value="pengeluaran" {{ old('tipe', $transaction->tipe) === 'pengeluaran' ? 'checked' : '' }} onchange="switchTipe('pengeluaran')" style="display: none;">
                        <i class="fa-solid fa-arrow-up" style="color: #ef4444;"></i> Pengeluaran
                    </label>
                </div>
                @error('tipe')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <!-- Kategori -->
            <div class="form-group">
                <label for="category_id" class="form-label">Kategori</label>
                <select name="category_id" id="category_id" class="form-control" required>
                    <option value="">-- Pilih Kategori --</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" data-tipe="{{ $cat->tipe }}" {{ old('category_id', $transaction->category_id) == $cat->id ? 'selected' : '' }}>
                            {{ $cat->nama }} {{ $cat->user_id ? '(Kustom)' : '' }}
                        </option>
                    @endforeach
                </select>
                @error('category_id')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <!-- Nominal Transaksi -->
            <div class="form-group">
                <label for="jumlah" class="form-label">Nominal (Rp)</label>
                <input type="number" id="jumlah" name="jumlah" class="form-control" style="font-size: 1.15rem; font-weight: 700;" min="1" step="1" value="{{ old('jumlah', (int) $transaction->jumlah) }}" required>
                @error('jumlah')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <!-- Tanggal -->
            <div class="form-group">
                <label for="tanggal" class="form-label">Tanggal Transaksi</label>
                <input type="date" id="tanggal" name="tanggal" class="form-control" value="{{ old('tanggal', $transaction->tanggal->format('Y-m-d')) }}" required>
                @error('tanggal')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <!-- Keterangan -->
            <div class="form-group">
                <label for="keterangan" class="form-label">Keterangan / Catatan Tambahan</label>
                <textarea id="keterangan" name="keterangan" class="form-control" rows="3">{{ old('keterangan', $transaction->keterangan) }}</textarea>
                @error('keterangan')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div style="margin-top: 2rem; display: flex; justify-content: flex-end; gap: 0.75rem;">
                <a href="{{ route('transactions.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function switchTipe(tipe) {
        const labelIn = document.getElementById('labelPemasukan');
        const labelOut = document.getElementById('labelPengeluaran');
        const catSelect = document.getElementById('category_id');

        if (tipe === 'pemasukan') {
            labelIn.style.borderColor = '#10b981';
            labelIn.style.backgroundColor = '#ecfdf5';
            labelIn.style.color = '#065f46';
            labelOut.style.borderColor = '#cbd5e1';
            labelOut.style.backgroundColor = 'transparent';
            labelOut.style.color = '#475569';
        } else {
            labelOut.style.borderColor = '#ef4444';
            labelOut.style.backgroundColor = '#fef2f2';
            labelOut.style.color = '#991b1b';
            labelIn.style.borderColor = '#cbd5e1';
            labelIn.style.backgroundColor = 'transparent';
            labelIn.style.color = '#475569';
        }

        Array.from(catSelect.options).forEach(opt => {
            if (!opt.value) return;
            const optTipe = opt.getAttribute('data-tipe');
            if (optTipe === tipe) {
                opt.style.display = '';
            } else {
                opt.style.display = 'none';
                if (opt.selected) {
                    opt.selected = false;
                }
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        const initialTipe = document.querySelector('input[name="tipe"]:checked')?.value || '{{ $transaction->tipe }}';
        switchTipe(initialTipe);
    });
</script>
@endsection
