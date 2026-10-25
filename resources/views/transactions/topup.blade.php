@extends('layouts.app', ['title' => 'Isi Saldo'])

@section('content')
<div style="max-width: 600px; margin: 0 auto;">
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem;">
        <div>
            <h1 style="font-size: 1.5rem; font-weight: 800; color: #0f172a;">Pengisian Saldo</h1>
            <p style="color: #64748b; font-size: 0.925rem;">Tambahkan dana ke saldo dompet keuangan Anda</p>
        </div>
        <a href="{{ route('dashboard') }}" class="btn btn-secondary btn-sm">
            <i class="fa-solid fa-arrow-left"></i> Kembali
        </a>
    </div>

    <!-- Saldo Saat Ini Box -->
    <div class="card" style="padding: 1.25rem 1.5rem; background: linear-gradient(135deg, #ecfdf5, #d1fae5); border-color: #a7f3d0; margin-bottom: 1.5rem;">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <span style="font-size: 0.85rem; font-weight: 600; color: #065f46; text-transform: uppercase;">Saldo Saat Ini</span>
                <div style="font-size: 1.6rem; font-weight: 800; color: #047857;">
                    Rp {{ number_format($balance, 0, ',', '.') }}
                </div>
            </div>
            <div style="width: 48px; height: 48px; background: white; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; color: #059669; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
                <i class="fa-solid fa-wallet"></i>
            </div>
        </div>
    </div>

    <!-- Form Top Up -->
    <div class="card">
        <form action="{{ route('topup.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="jumlah" class="form-label">Nominal Pengisian (Rp)</label>
                <div style="position: relative;">
                    <input type="number" id="jumlah" name="jumlah" class="form-control" style="font-size: 1.25rem; font-weight: 700; padding-left: 1rem;" min="1000" step="1000" value="{{ old('jumlah') }}" required placeholder="Contoh: 500000">
                </div>
                @error('jumlah')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <!-- Pilihan Cepat Nominal -->
            <div style="margin-bottom: 1.5rem;">
                <label class="form-label" style="font-size: 0.85rem; color: #64748b;">Pilih Cepat Nominal:</label>
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.5rem;">
                    <button type="button" class="btn btn-secondary btn-sm" onclick="setNominal(50000)">+50 Rb</button>
                    <button type="button" class="btn btn-secondary btn-sm" onclick="setNominal(100000)">+100 Rb</button>
                    <button type="button" class="btn btn-secondary btn-sm" onclick="setNominal(250000)">+250 Rb</button>
                    <button type="button" class="btn btn-secondary btn-sm" onclick="setNominal(500000)">+500 Rb</button>
                    <button type="button" class="btn btn-secondary btn-sm" onclick="setNominal(1000000)">+1 Juta</button>
                    <button type="button" class="btn btn-secondary btn-sm" onclick="setNominal(2500000)">+2.5 Juta</button>
                </div>
            </div>

            <div class="form-group">
                <label for="tanggal" class="form-label">Tanggal Transaksi</label>
                <input type="date" id="tanggal" name="tanggal" class="form-control" value="{{ old('tanggal', date('Y-m-d')) }}" required>
                @error('tanggal')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="keterangan" class="form-label">Keterangan (Opsional)</label>
                <input type="text" id="keterangan" name="keterangan" class="form-control" value="{{ old('keterangan', 'Top Up Saldo Dompet') }}" placeholder="Contoh: Setoran uang tunai / transfer bank">
                @error('keterangan')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div style="margin-top: 1.75rem;">
                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.8rem; font-size: 1rem;">
                    <i class="fa-solid fa-check"></i> Konfirmasi & Tambah Saldo
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function setNominal(amount) {
        document.getElementById('jumlah').value = amount;
    }
</script>
@endsection
