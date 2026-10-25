# Aplikasi Pencatatan Keuangan (Laravel + MySQL + Blade)

Aplikasi web **Pencatatan Keuangan** (frontend + backend + database) yang dirancang untuk mengelola keuangan pribadi dengan pembagian peran (Role) **Admin** dan **User**. Dibangun menggunakan **Laravel 11**, **MySQL**, dan **Blade**, rapi, aman, dan mudah dipahami.

---

## 🚀 Fitur Utama

### 1. Fitur Umum (Admin & User)
- **Registrasi Akun Baru**: Pengguna baru dapat mendaftar sendiri (role otomatis `user` dan status `aktif`).
- **Autentikasi Aman**: Login dengan proteksi password hash (bcrypt), sesi terenkripsi, proteksi CSRF, serta pencegahan login untuk akun yang dinonaktifkan.
- **Pengaturan Profil**: Pengguna dapat melihat data akun, mengubah nama, email, serta mengganti kata sandi.
- **Logout Aman**: Mengakhiri sesi dan menghapus token autentikasi.

### 2. Fitur User (Pengguna Layanan)
- **Isi Saldo (Top-Up)**: Fitur penambahan saldo instan dengan tombol cepat (+50Rb, +100Rb, +500Rb, +1Juta, dll.) yang otomatis dicatat sebagai transaksi pemasukan kategori *Isi Saldo*.
- **Dashboard Ringkasan Keuangan**:
  - 3 Kartu Statistik: **Saldo Saat Ini**, **Total Pemasukan**, dan **Total Pengeluaran** (Format Rupiah: `Rp 1.500.000`).
  - Tabel 5 Transaksi Terbaru beserta keterangan dan jenis transaksi.
  - Grafik mini pos persentase pengeluaran terbesar.
- **Catatan Transaksi (CRUD)**:
  - Tambah catatan pemasukan & pengeluaran (nominal, tanggal, keterangan, kategori).
  - Edit catatan transaksi yang sudah ada.
  - Hapus catatan transaksi dengan dialog konfirmasi.
  - Filter riwayat berdasarkan jenis transaksi (Pemasukan/Pengeluaran), kategori, dan rentang tanggal.
- **Manajemen Kategori Kustom**:
  - Memilih kategori bawaan sistem (*Gaji, Uang Saku, Bonus, Makan & Minum, Transportasi, Belanja, Tagihan, dll.*).
  - Membuat kategori kustom sendiri yang **hanya terlihat dan hanya dapat digunakan oleh user pembuatnya**.
  - Mengubah dan menghapus kategori kustom pribadi.

### 3. Fitur Admin (Pengelola Sistem)
- **Daftar Seluruh Pengguna**: Melihat semua akun user, role, status aktif/nonaktif, jumlah transaksi, dan tanggal registrasi.
- **Kelola Status Akun**: Mengaktifkan atau menonaktifkan akun user dengan 1 klik (akun nonaktif langsung dicegat saat login maupun saat sedang beraktivitas).
- **Hapus Akun Pengguna**: Menghapus akun user beserta relasi datanya (cascade delete).
- **Proteksi Akun Sendiri**: Admin dicegah menonaktifkan atau menghapus akun administratornya sendiri.

---

## 🛡️ Ketentuan Keamanan & Aturan Bisnis

1. **Validasi Kepemilikan Data (Server-Side Ownership)**: Setiap operasi baca, ubah, dan hapus pada transaksi serta kategori dibatasi oleh `where('user_id', Auth::id())`. Akses URL dengan manipulasi ID milik pengguna lain langsung ditolak dengan respon **`403 Forbidden`**.
2. **Perhitungan Saldo Real-time**: Saldo dihitung langsung dari basis data (`Saldo = Total Pemasukan - Total Pengeluaran`).
3. **Pencegahan Saldo Negatif**: Sistem otomatis menolak transaksi pengeluaran baru atau pengeditan transaksi yang menyebabkan saldo akhir menjadi minus.
4. **Proteksi Role di Sisi Server**: Middleware `CheckRole` memblokir user biasa yang mencoba mengakses halaman/rute admin dan menyajikan tampilan error 403.
5. **Proteksi Akun Nonaktif**: Middleware `CheckStatus` otomatis mengeluarkan sesi akun yang telah dinonaktifkan oleh administrator.
6. **Keamanan Input**: Seluruh input divalidasi ketat di server, terbebas dari SQL Injection melalui Eloquent ORM parameterization, dan terlindungi dari XSS via Blade escaping.

---

## 📋 Akun Demo Siap Pakai

Database seeder telah menyediakan akun demo dengan data transaksi awal:

| Role | Nama Akun | Email | Password | Status |
|---|---|---|---|---|
| **Admin** | Administrator Keuangan | `admin@keuangan.test` | `admin123` | **Aktif** |
| **User 1** | Budi Santoso | `budi@keuangan.test` | `user123` | **Aktif** |
| **User 2** | Siti Rahma | `siti@keuangan.test` | `user123` | **Aktif** |
| **User Nonaktif** | User Nonaktif | `nonaktif@keuangan.test` | `user123` | **Nonaktif** |

*(Tersedia tombol pintas pada halaman login untuk mengisi email dan password demo secara otomatis).*

---

## 💻 Panduan Instalasi & Menjalankan Aplikasi

### 1. Prasyarat
- PHP >= 8.2 (ekstensi `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml` aktif)
- Composer
- MySQL Server (misal lewat Laragon atau XAMPP)

### 2. Konfigurasi Database (`.env`)
Pastikan database MySQL bernama `db_keuangan` telah dibuat:
```sql
CREATE DATABASE db_keuangan CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Konfigurasi file `.env` di direktori utama:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=db_keuangan
DB_USERNAME=root
DB_PASSWORD=
```

### 3. Migrasi & Seeder Database
Jalankan migrasi tabel dan seeder data awal dengan perintah:
```bash
php artisan migrate:fresh --seed
```

### 4. Menjalankan Server Lokal
Jalankan server pengembangan Laravel:
```bash
php artisan serve
```

Aplikasi dapat langsung dibuka melalui peramban web di:
```text
http://127.0.0.1:8000
```

---

## 🧪 Pengujian Otomatis (PHPUnit Feature Tests)

Untuk menjalankan seluruh rangkaian pengujian fitur otomatis (mencakup registrasi, proteksi saldo negatif, isolasi data antar user, larangan akses admin untuk user biasa, dan pencegahan login akun nonaktif):

```bash
php artisan test
```

Semua 10 pengujian fitur lulus 100% (*passed*).
