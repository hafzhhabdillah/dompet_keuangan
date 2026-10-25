@extends('layouts.app', ['title' => 'Kelola Kategori'])

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem;">
    <div>
        <h1 style="font-size: 1.65rem; font-weight: 800; color: #0f172a;">Kategori Keuangan</h1>
        <p style="color: #64748b; font-size: 0.95rem;">Kelola pos transaksi bawaan sistem dan kategori buatan Anda sendiri</p>
    </div>
    <a href="{{ route('categories.create') }}" class="btn btn-primary">
        <i class="fa-solid fa-plus"></i> Buat Kategori Kustom
    </a>
</div>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; align-items: start;">
    <!-- Kategori Khusus Milik User -->
    <div class="card">
        <div class="card-header">
            <div>
                <h2 class="card-title">
                    <i class="fa-solid fa-user-tag" style="color: var(--primary);"></i> Kategori Kustom Anda
                </h2>
                <small style="color: #64748b; font-size: 0.8rem;">Hanya terlihat dan dapat digunakan oleh akun Anda.</small>
            </div>
        </div>

        @if($userCategories->isEmpty())
            <div style="text-align: center; padding: 2rem 1rem;">
                <div style="font-size: 2.25rem; color: #cbd5e1; margin-bottom: 0.5rem;">
                    <i class="fa-solid fa-tags"></i>
                </div>
                <h3 style="font-size: 1rem; font-weight: 700; color: #334155; margin-bottom: 0.25rem;">Belum Ada Kategori Kustom</h3>
                <p style="color: #64748b; font-size: 0.85rem; margin-bottom: 1rem;">Anda dapat membuat kategori sendiri sesuai kebutuhan unik Anda.</p>
                <a href="{{ route('categories.create') }}" class="btn btn-secondary btn-sm">+ Tambah Sekarang</a>
            </div>
        @else
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nama Kategori</th>
                            <th>Tipe</th>
                            <th style="text-align: center;">Digunakan</th>
                            <th style="text-align: center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($userCategories as $cat)
                            <tr>
                                <td>
                                    <strong style="color: #0f172a;">{{ $cat->nama }}</strong>
                                </td>
                                <td>
                                    <span class="badge {{ $cat->tipe === 'pemasukan' ? 'badge-income' : 'badge-expense' }}">
                                        {{ ucfirst($cat->tipe) }}
                                    </span>
                                </td>
                                <td style="text-align: center; color: #64748b; font-size: 0.85rem;">
                                    {{ $cat->transactions_count }} transaksi
                                </td>
                                <td style="text-align: center; white-space: nowrap;">
                                    <div style="display: inline-flex; gap: 0.35rem;">
                                        <a href="{{ route('categories.edit', $cat) }}" class="btn btn-secondary btn-sm" title="Edit">
                                            <i class="fa-solid fa-pen"></i>
                                        </a>
                                        <form action="{{ route('categories.destroy', $cat) }}" method="POST" style="display: inline;" onsubmit="return confirm('Hapus kategori ini? Seluruh transaksi terkait juga akan terpengaruh.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
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
        @endif
    </div>

    <!-- Kategori Bawaan Sistem -->
    <div class="card">
        <div class="card-header">
            <div>
                <h2 class="card-title">
                    <i class="fa-solid fa-shield-halved" style="color: #0284c7;"></i> Kategori Bawaan Sistem
                </h2>
                <small style="color: #64748b; font-size: 0.8rem;">Disediakan otomatis oleh sistem dan siap dipakai.</small>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nama Kategori</th>
                        <th>Tipe</th>
                        <th style="text-align: right;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($defaultCategories as $dCat)
                        <tr>
                            <td>
                                <strong style="color: #334155;">{{ $dCat->nama }}</strong>
                            </td>
                            <td>
                                <span class="badge {{ $dCat->tipe === 'pemasukan' ? 'badge-income' : 'badge-expense' }}">
                                    {{ ucfirst($dCat->tipe) }}
                                </span>
                            </td>
                            <td style="text-align: right;">
                                <span class="badge badge-system">Bawaan</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
