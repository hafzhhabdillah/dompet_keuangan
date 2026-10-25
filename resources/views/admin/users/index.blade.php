@extends('layouts.app', ['title' => 'Manajemen Pengguna - Admin'])

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem;">
    <div>
        <h1 style="font-size: 1.65rem; font-weight: 800; color: #0f172a;">Manajemen Akun Pengguna</h1>
        <p style="color: #64748b; font-size: 0.95rem;">Panel kontrol administrator untuk mengelola status akses seluruh akun user</p>
    </div>
</div>

<!-- 3 Kartu Statistik Pengguna -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.25rem; margin-bottom: 1.75rem;">
    <div class="card" style="padding: 1.25rem; margin-bottom: 0; display: flex; align-items: center; gap: 1rem;">
        <div style="width: 50px; height: 50px; background: #e0f2fe; color: #0284c7; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.4rem;">
            <i class="fa-solid fa-users"></i>
        </div>
        <div>
            <div style="font-size: 0.8rem; font-weight: 600; color: #64748b; text-transform: uppercase;">Total Pengguna</div>
            <div style="font-size: 1.5rem; font-weight: 800; color: #0f172a;">{{ $totalUsers }}</div>
        </div>
    </div>

    <div class="card" style="padding: 1.25rem; margin-bottom: 0; display: flex; align-items: center; gap: 1rem;">
        <div style="width: 50px; height: 50px; background: #dcfce7; color: #16a34a; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.4rem;">
            <i class="fa-solid fa-user-check"></i>
        </div>
        <div>
            <div style="font-size: 0.8rem; font-weight: 600; color: #64748b; text-transform: uppercase;">Akun Aktif</div>
            <div style="font-size: 1.5rem; font-weight: 800; color: #15803d;">{{ $activeUsers }}</div>
        </div>
    </div>

    <div class="card" style="padding: 1.25rem; margin-bottom: 0; display: flex; align-items: center; gap: 1rem;">
        <div style="width: 50px; height: 50px; background: #fee2e2; color: #dc2626; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.4rem;">
            <i class="fa-solid fa-user-slash"></i>
        </div>
        <div>
            <div style="font-size: 0.8rem; font-weight: 600; color: #64748b; text-transform: uppercase;">Akun Dinonaktifkan</div>
            <div style="font-size: 1.5rem; font-weight: 800; color: #b91c1c;">{{ $inactiveUsers }}</div>
        </div>
    </div>
</div>

<!-- Filter & Search Box -->
<div class="card" style="padding: 1.25rem; margin-bottom: 1.5rem;">
    <form action="{{ route('admin.users.index') }}" method="GET" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem; align-items: end;">
        <div>
            <label class="form-label" style="font-size: 0.825rem;">Cari Nama / Email</label>
            <input type="text" name="search" class="form-control" style="font-size: 0.875rem;" placeholder="Ketik kata kunci..." value="{{ request('search') }}">
        </div>

        <div>
            <label class="form-label" style="font-size: 0.825rem;">Filter Role</label>
            <select name="role" class="form-control" style="font-size: 0.875rem;">
                <option value="">-- Semua Role --</option>
                <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Administrator</option>
                <option value="user" {{ request('role') === 'user' ? 'selected' : '' }}>User Biasa</option>
            </select>
        </div>

        <div>
            <label class="form-label" style="font-size: 0.825rem;">Filter Status</label>
            <select name="status" class="form-control" style="font-size: 0.875rem;">
                <option value="">-- Semua Status --</option>
                <option value="aktif" {{ request('status') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                <option value="nonaktif" {{ request('status') === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
            </select>
        </div>

        <div style="display: flex; gap: 0.5rem;">
            <button type="submit" class="btn btn-secondary btn-sm" style="flex: 1; padding: 0.65rem;">
                <i class="fa-solid fa-magnifying-glass"></i> Cari
            </button>
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary btn-sm" style="padding: 0.65rem;" title="Reset Filter">
                <i class="fa-solid fa-rotate-left"></i>
            </a>
        </div>
    </form>
</div>

<!-- Tabel Pengguna -->
<div class="card" style="padding: 0; overflow: hidden;">
    @if($users->isEmpty())
        <div style="text-align: center; padding: 3rem 1.5rem;">
            <div style="font-size: 2.5rem; color: #cbd5e1; margin-bottom: 0.5rem;">
                <i class="fa-solid fa-users-slash"></i>
            </div>
            <h3 style="font-size: 1.1rem; font-weight: 700; color: #334155;">Tidak Ada Data Pengguna</h3>
            <p style="color: #64748b; font-size: 0.875rem;">Tidak ditemukan pengguna yang sesuai dengan pencarian Anda.</p>
        </div>
    @else
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Pengguna</th>
                        <th>Role</th>
                        <th>Status Akun</th>
                        <th>Transaksi</th>
                        <th>Terdaftar</th>
                        <th style="text-align: center;">Tindakan Akun</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $u)
                        <tr>
                            <td style="color: #64748b; font-weight: 600;">#{{ $u->id }}</td>
                            <td>
                                <strong style="color: #0f172a; display: block;">{{ $u->nama }}</strong>
                                <small style="color: #64748b;">{{ $u->email }}</small>
                            </td>
                            <td>
                                <span class="badge-role {{ $u->role }}">
                                    {{ ucfirst($u->role) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge {{ $u->status === 'aktif' ? 'badge-active' : 'badge-inactive' }}">
                                    <i class="fa-solid fa-circle" style="font-size: 0.45rem;"></i> {{ ucfirst($u->status) }}
                                </span>
                            </td>
                            <td>
                                <span style="font-weight: 600; color: #334155;">{{ $u->transactions_count }}</span> catatan
                            </td>
                            <td style="color: #64748b; font-size: 0.85rem; white-space: nowrap;">
                                {{ $u->created_at->translatedFormat('d M Y') }}
                            </td>
                            <td style="text-align: center; white-space: nowrap;">
                                @if($u->id === Auth::id())
                                    <span style="font-size: 0.75rem; background: #f1f5f9; color: #64748b; padding: 0.25rem 0.5rem; border-radius: 6px;">
                                        Akun Anda Saat Ini
                                    </span>
                                @else
                                    <div style="display: inline-flex; gap: 0.35rem;">
                                        <!-- Tombol Aktif / Nonaktifkan -->
                                        <form action="{{ route('admin.users.toggle-status', $u) }}" method="POST" style="display: inline;" onsubmit="return confirm('Apakah Anda yakin ingin {{ $u->status === 'aktif' ? 'menonaktifkan' : 'mengaktifkan kembali' }} akun {{ $u->nama }}?')">
                                            @csrf
                                            @method('PATCH')
                                            @if($u->status === 'aktif')
                                                <button type="submit" class="btn btn-secondary btn-sm" style="color: #b91c1c; border-color: #fecaca; background: #fef2f2;" title="Nonaktifkan Akun">
                                                    <i class="fa-solid fa-user-xmark"></i> Nonaktifkan
                                                </button>
                                            @else
                                                <button type="submit" class="btn btn-secondary btn-sm" style="color: #15803d; border-color: #bbf7d0; background: #f0fdf4;" title="Aktifkan Akun">
                                                    <i class="fa-solid fa-user-check"></i> Aktifkan
                                                </button>
                                            @endif
                                        </form>

                                        <!-- Tombol Hapus Akun -->
                                        <form action="{{ route('admin.users.destroy', $u) }}" method="POST" style="display: inline;" onsubmit="return confirm('PERINGATAN: Menghapus akun {{ $u->nama }} akan menghapus seluruh data catatan keuangan dan kategorinya secara permanen. Lanjutkan?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" title="Hapus Permanen">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div style="padding: 1rem 1.5rem; border-top: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; font-size: 0.875rem; color: #64748b;">
            <div>
                Menampilkan <strong>{{ $users->firstItem() ?? 0 }}</strong> - <strong>{{ $users->lastItem() ?? 0 }}</strong> dari <strong>{{ $users->total() }}</strong> pengguna
            </div>
            <div>
                {{ $users->links() }}
            </div>
        </div>
    @endif
</div>
@endsection
