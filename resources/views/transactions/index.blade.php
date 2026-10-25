@extends('layouts.app', ['title' => 'Riwayat Transaksi'])

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem;">
    <div>
        <h1 style="font-size: 1.65rem; font-weight: 800; color: #0f172a;">Riwayat Catatan Keuangan</h1>
        <p style="color: #64748b; font-size: 0.95rem;">Daftar seluruh catatan pemasukan dan pengeluaran Anda</p>
    </div>
    <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
        <a href="{{ route('transactions.create') }}" class="btn btn-primary">
            <i class="fa-solid fa-plus"></i> Tambah Transaksi
        </a>
        <a href="{{ route('topup.index') }}" class="btn btn-secondary">
            <i class="fa-solid fa-wallet"></i> Isi Saldo
        </a>
    </div>
</div>

<!-- Filter Card -->
<div class="card" style="padding: 1.25rem 1.5rem; margin-bottom: 1.5rem;">
    <form action="{{ route('transactions.index') }}" method="GET" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; align-items: end;">
        <div>
            <label class="form-label" style="font-size: 0.825rem;">Jenis Transaksi</label>
            <select name="tipe" class="form-control" style="font-size: 0.875rem;">
                <option value="">-- Semua Jenis --</option>
                <option value="pemasukan" {{ request('tipe') === 'pemasukan' ? 'selected' : '' }}>Pemasukan</option>
                <option value="pengeluaran" {{ request('tipe') === 'pengeluaran' ? 'selected' : '' }}>Pengeluaran</option>
            </select>
        </div>

        <div>
            <label class="form-label" style="font-size: 0.825rem;">Kategori</label>
            <select name="category_id" class="form-control" style="font-size: 0.875rem;">
                <option value="">-- Semua Kategori --</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                        [{{ ucfirst($cat->tipe) }}] {{ $cat->nama }} {{ $cat->user_id ? '(Kustom)' : '' }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="form-label" style="font-size: 0.825rem;">Dari Tanggal</label>
            <input type="date" name="start_date" class="form-control" style="font-size: 0.875rem;" value="{{ request('start_date') }}">
        </div>

        <div>
            <label class="form-label" style="font-size: 0.825rem;">Sampai Tanggal</label>
            <input type="date" name="end_date" class="form-control" style="font-size: 0.875rem;" value="{{ request('end_date') }}">
        </div>

        <div style="display: flex; gap: 0.5rem;">
            <button type="submit" class="btn btn-secondary btn-sm" style="flex: 1; padding: 0.65rem;">
                <i class="fa-solid fa-filter"></i> Filter
            </button>
            <a href="{{ route('transactions.index') }}" class="btn btn-secondary btn-sm" style="padding: 0.65rem;" title="Reset Filter">
                <i class="fa-solid fa-rotate-left"></i>
            </a>
        </div>
    </form>
</div>

<!-- Tabel Transaksi -->
<div class="card" style="padding: 0; overflow: hidden;">
    @if($transactions->isEmpty())
        <div style="text-align: center; padding: 3rem 1.5rem;">
            <div style="font-size: 3rem; color: #cbd5e1; margin-bottom: 0.75rem;">
                <i class="fa-regular fa-folder-open"></i>
            </div>
            <h3 style="font-size: 1.15rem; font-weight: 700; color: #334155; margin-bottom: 0.25rem;">Tidak Ada Catatan Transaksi</h3>
            <p style="color: #64748b; font-size: 0.9rem; margin-bottom: 1.25rem;">Tidak ada transaksi yang cocok dengan filter yang Anda tentukan.</p>
            <a href="{{ route('transactions.create') }}" class="btn btn-primary btn-sm">Buat Catatan Sekarang</a>
        </div>
    @else
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 140px;">Tanggal</th>
                        <th>Kategori</th>
                        <th>Keterangan</th>
                        <th style="width: 120px;">Jenis</th>
                        <th style="text-align: right; width: 180px;">Jumlah</th>
                        <th style="text-align: center; width: 120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transactions as $trx)
                        <tr>
                            <td style="white-space: nowrap; font-weight: 600; color: #475569;">
                                {{ \Carbon\Carbon::parse($trx->tanggal)->translatedFormat('d M Y') }}
                            </td>
                            <td>
                                <strong style="color: #1e293b;">{{ $trx->category->nama ?? 'Tanpa Kategori' }}</strong>
                                @if($trx->category && $trx->category->user_id)
                                    <span style="font-size: 0.7rem; background: #e0e7ff; color: #3730a3; padding: 0.1rem 0.4rem; border-radius: 4px; margin-left: 0.25rem;">Kustom</span>
                                @endif
                            </td>
                            <td style="color: #64748b; font-size: 0.875rem;">
                                {{ $trx->keterangan ?: '-' }}
                            </td>
                            <td>
                                <span class="badge {{ $trx->tipe === 'pemasukan' ? 'badge-income' : 'badge-expense' }}">
                                    <i class="fa-solid {{ $trx->tipe === 'pemasukan' ? 'fa-arrow-down' : 'fa-arrow-up' }}" style="font-size: 0.65rem;"></i>
                                    {{ ucfirst($trx->tipe) }}
                                </span>
                            </td>
                            <td style="text-align: right; font-weight: 800; white-space: nowrap; color: {{ $trx->tipe === 'pemasukan' ? '#059669' : '#dc2626' }};">
                                {{ $trx->tipe === 'pemasukan' ? '+' : '-' }} Rp {{ number_format($trx->jumlah, 0, ',', '.') }}
                            </td>
                            <td style="text-align: center; white-space: nowrap;">
                                <div style="display: inline-flex; gap: 0.35rem;">
                                    <a href="{{ route('transactions.edit', $trx) }}" class="btn btn-secondary btn-sm" title="Edit Transaksi">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <form action="{{ route('transactions.destroy', $trx) }}" method="POST" style="display: inline;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus catatan transaksi ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" title="Hapus Transaksi">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div style="padding: 1rem 1.5rem; border-top: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; font-size: 0.875rem; color: #64748b;">
            <div>
                Menampilkan <strong>{{ $transactions->firstItem() ?? 0 }}</strong> - <strong>{{ $transactions->lastItem() ?? 0 }}</strong> dari <strong>{{ $transactions->total() }}</strong> transaksi
            </div>
            <div>
                {{ $transactions->links() }}
            </div>
        </div>
    @endif
</div>
@endsection
