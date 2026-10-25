@extends('layouts.app', ['title' => 'Dashboard Keuangan'])

@push('styles')
<style>
    .summary-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1.25rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: white;
        border-radius: var(--radius-lg);
        padding: 1.5rem;
        border: 1px solid var(--border-color);
        box-shadow: var(--shadow-sm);
        display: flex;
        align-items: center;
        gap: 1.25rem;
        position: relative;
        overflow: hidden;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        bottom: 0;
        width: 5px;
    }

    .stat-card.balance::before { background-color: #0284c7; }
    .stat-card.income::before { background-color: #10b981; }
    .stat-card.expense::before { background-color: #ef4444; }

    .stat-icon {
        width: 56px;
        height: 56px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.6rem;
        flex-shrink: 0;
    }

    .stat-card.balance .stat-icon {
        background-color: #e0f2fe;
        color: #0284c7;
    }

    .stat-card.income .stat-icon {
        background-color: var(--income-bg);
        color: var(--income);
    }

    .stat-card.expense .stat-icon {
        background-color: var(--expense-bg);
        color: var(--expense);
    }

    .stat-info {
        flex: 1;
    }

    .stat-label {
        font-size: 0.85rem;
        font-weight: 600;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 0.25rem;
    }

    .stat-value {
        font-size: 1.5rem;
        font-weight: 800;
        color: var(--text-main);
        line-height: 1.2;
    }

    .quick-actions {
        display: flex;
        gap: 0.75rem;
        flex-wrap: wrap;
        margin-bottom: 1.75rem;
    }
</style>
@endpush

@section('content')
<!-- Header Salam & Quick Action -->
<div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.75rem;">
    <div>
        <h1 style="font-size: 1.65rem; font-weight: 800; color: #0f172a;">Halo, {{ $user->nama }}! 👋</h1>
        <p style="color: #64748b; font-size: 0.95rem;">Berikut adalah ringkasan arus kas keuangan pribadi Anda hari ini.</p>
    </div>
    <div class="quick-actions" style="margin-bottom: 0;">
        <a href="{{ route('topup.index') }}" class="btn btn-primary">
            <i class="fa-solid fa-circle-plus"></i> Isi Saldo
        </a>
        <a href="{{ route('transactions.create', ['tipe' => 'pemasukan']) }}" class="btn btn-secondary">
            <i class="fa-solid fa-arrow-down" style="color: #10b981;"></i> Tambah Pemasukan
        </a>
        <a href="{{ route('transactions.create', ['tipe' => 'pengeluaran']) }}" class="btn btn-secondary">
            <i class="fa-solid fa-arrow-up" style="color: #ef4444;"></i> Tambah Pengeluaran
        </a>
    </div>
</div>

<!-- 3 Kartu Ringkasan (Saldo, Pemasukan, Pengeluaran) -->
<div class="summary-grid">
    <!-- Card Saldo -->
    <div class="stat-card balance">
        <div class="stat-icon">
            <i class="fa-solid fa-wallet"></i>
        </div>
        <div class="stat-info">
            <div class="stat-label">Saldo Saat Ini</div>
            <div class="stat-value" style="color: #0369a1;">
                Rp {{ number_format($balance, 0, ',', '.') }}
            </div>
            <span style="font-size: 0.75rem; color: #64748b;">(Pemasukan - Pengeluaran)</span>
        </div>
    </div>

    <!-- Card Total Pemasukan -->
    <div class="stat-card income">
        <div class="stat-icon">
            <i class="fa-solid fa-arrow-trend-up"></i>
        </div>
        <div class="stat-info">
            <div class="stat-label">Total Pemasukan</div>
            <div class="stat-value" style="color: #065f46;">
                Rp {{ number_format($totalIncome, 0, ',', '.') }}
            </div>
            <span style="font-size: 0.75rem; color: #10b981;">Seluruh pemasukan tercatat</span>
        </div>
    </div>

    <!-- Card Total Pengeluaran -->
    <div class="stat-card expense">
        <div class="stat-icon">
            <i class="fa-solid fa-arrow-trend-down"></i>
        </div>
        <div class="stat-info">
            <div class="stat-label">Total Pengeluaran</div>
            <div class="stat-value" style="color: #991b1b;">
                Rp {{ number_format($totalExpense, 0, ',', '.') }}
            </div>
            <span style="font-size: 0.75rem; color: #ef4444;">Seluruh pengeluaran tercatat</span>
        </div>
    </div>
</div>

<!-- Grid Transaksi Terbaru & Pengeluaran Terbesar -->
<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem; align-items: start;">
    <!-- Tabel Transaksi Terbaru -->
    <div class="card" style="margin-bottom: 0;">
        <div class="card-header">
            <h2 class="card-title">
                <i class="fa-solid fa-receipt" style="color: var(--primary);"></i> Transaksi Terbaru
            </h2>
            <a href="{{ route('transactions.index') }}" class="btn btn-secondary btn-sm">
                Lihat Semua <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>

        @if($recentTransactions->isEmpty())
            <div style="text-align: center; padding: 2.5rem 1rem;">
                <div style="font-size: 2.5rem; color: #cbd5e1; margin-bottom: 0.75rem;">
                    <i class="fa-regular fa-folder-open"></i>
                </div>
                <h3 style="font-size: 1.05rem; font-weight: 700; color: #334155; margin-bottom: 0.25rem;">Belum Ada Transaksi</h3>
                <p style="color: #64748b; font-size: 0.875rem; margin-bottom: 1.25rem;">Mulai catat transaksi pertama atau isi saldo Anda.</p>
                <a href="{{ route('topup.index') }}" class="btn btn-primary btn-sm">Isi Saldo Sekarang</a>
            </div>
        @else
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Kategori & Keterangan</th>
                            <th>Tipe</th>
                            <th style="text-align: right;">Jumlah</th>
                            <th style="text-align: center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentTransactions as $trx)
                            <tr>
                                <td style="white-space: nowrap; color: #475569;">
                                    {{ \Carbon\Carbon::parse($trx->tanggal)->translatedFormat('d M Y') }}
                                </td>
                                <td>
                                    <strong style="color: #1e293b; display: block;">{{ $trx->category->nama ?? 'Tanpa Kategori' }}</strong>
                                    <small style="color: #64748b;">{{ $trx->keterangan ?: '-' }}</small>
                                </td>
                                <td>
                                    <span class="badge {{ $trx->tipe === 'pemasukan' ? 'badge-income' : 'badge-expense' }}">
                                        {{ ucfirst($trx->tipe) }}
                                    </span>
                                </td>
                                <td style="text-align: right; font-weight: 700; white-space: nowrap; color: {{ $trx->tipe === 'pemasukan' ? '#059669' : '#dc2626' }};">
                                    {{ $trx->tipe === 'pemasukan' ? '+' : '-' }} Rp {{ number_format($trx->jumlah, 0, ',', '.') }}
                                </td>
                                <td style="text-align: center; white-space: nowrap;">
                                    <a href="{{ route('transactions.edit', $trx) }}" class="btn btn-secondary btn-sm" title="Edit">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <!-- Ringkasan Kategori Pengeluaran -->
    <div class="card" style="margin-bottom: 0;">
        <div class="card-header">
            <h2 class="card-title">
                <i class="fa-solid fa-chart-simple" style="color: #ef4444;"></i> Pos Pengeluaran
            </h2>
        </div>
        @if($topExpenses->isEmpty())
            <div style="text-align: center; padding: 2rem 0.5rem; color: #64748b; font-size: 0.875rem;">
                Belum ada pengeluaran yang tercatat.
            </div>
        @else
            <div style="display: flex; flex-direction: column; gap: 1rem;">
                @foreach($topExpenses as $item)
                    @php
                        $percentage = $totalExpense > 0 ? ($item->total / $totalExpense) * 100 : 0;
                    @endphp
                    <div>
                        <div style="display: flex; justify-content: space-between; font-size: 0.875rem; margin-bottom: 0.25rem;">
                            <span style="font-weight: 600; color: #1e293b;">{{ $item->category->nama ?? 'Lainnya' }}</span>
                            <span style="font-weight: 700; color: #dc2626;">Rp {{ number_format($item->total, 0, ',', '.') }}</span>
                        </div>
                        <div style="background-color: #f1f5f9; height: 8px; border-radius: 9999px; overflow: hidden;">
                            <div style="background-color: #ef4444; width: {{ $percentage }}%; height: 100%;"></div>
                        </div>
                        <div style="text-align: right; font-size: 0.75rem; color: #64748b; margin-top: 0.15rem;">
                            {{ number_format($percentage, 1) }}% dari total pengeluaran
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
