<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Akun Admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@keuangan.test'],
            [
                'nama' => 'Administrator Keuangan',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'status' => 'aktif',
            ]
        );

        // 2. Akun User Contoh
        $budi = User::firstOrCreate(
            ['email' => 'budi@keuangan.test'],
            [
                'nama' => 'Budi Santoso',
                'password' => Hash::make('user123'),
                'role' => 'user',
                'status' => 'aktif',
            ]
        );

        $siti = User::firstOrCreate(
            ['email' => 'siti@keuangan.test'],
            [
                'nama' => 'Siti Rahma',
                'password' => Hash::make('user123'),
                'role' => 'user',
                'status' => 'aktif',
            ]
        );

        // Akun nonaktif untuk pengujian login
        User::firstOrCreate(
            ['email' => 'nonaktif@keuangan.test'],
            [
                'nama' => 'User Nonaktif',
                'password' => Hash::make('user123'),
                'role' => 'user',
                'status' => 'nonaktif',
            ]
        );

        // 3. Kategori Bawaan (user_id = null)
        $categoriesPemasukan = [
            'Gaji',
            'Uang Saku',
            'Bonus',
            'Isi Saldo',
            'Lainnya (Pemasukan)',
        ];

        $categoriesPengeluaran = [
            'Makan & Minum',
            'Transportasi',
            'Belanja',
            'Pendidikan',
            'Hiburan',
            'Tagihan',
            'Lainnya (Pengeluaran)',
        ];

        $catModels = [];

        foreach ($categoriesPemasukan as $nama) {
            $cat = Category::firstOrCreate(
                ['nama' => $nama, 'tipe' => 'pemasukan', 'user_id' => null]
            );
            $catModels['in_'.$nama] = $cat;
        }

        foreach ($categoriesPengeluaran as $nama) {
            $cat = Category::firstOrCreate(
                ['nama' => $nama, 'tipe' => 'pengeluaran', 'user_id' => null]
            );
            $catModels['out_'.$nama] = $cat;
        }

        // 4. Kategori kustom untuk Budi (user_id = $budi->id)
        $budiCategory = Category::firstOrCreate([
            'nama' => 'Freelance Web Design',
            'tipe' => 'pemasukan',
            'user_id' => $budi->id,
        ]);

        // 5. Data Transaksi Awal untuk Budi Santoso
        if ($budi->transactions()->count() === 0) {
            Transaction::create([
                'user_id' => $budi->id,
                'category_id' => $catModels['in_Isi Saldo']->id,
                'tipe' => 'pemasukan',
                'jumlah' => 2000000,
                'tanggal' => Carbon::today()->subDays(5)->format('Y-m-d'),
                'keterangan' => 'Top up saldo awal dompet keuangan',
            ]);

            Transaction::create([
                'user_id' => $budi->id,
                'category_id' => $catModels['in_Gaji']->id,
                'tipe' => 'pemasukan',
                'jumlah' => 3500000,
                'tanggal' => Carbon::today()->subDays(3)->format('Y-m-d'),
                'keterangan' => 'Gaji bulanan kantor',
            ]);

            Transaction::create([
                'user_id' => $budi->id,
                'category_id' => $budiCategory->id,
                'tipe' => 'pemasukan',
                'jumlah' => 750000,
                'tanggal' => Carbon::today()->subDays(2)->format('Y-m-d'),
                'keterangan' => 'Projek freelance landing page',
            ]);

            Transaction::create([
                'user_id' => $budi->id,
                'category_id' => $catModels['out_Makan & Minum']->id,
                'tipe' => 'pengeluaran',
                'jumlah' => 125000,
                'tanggal' => Carbon::today()->subDays(2)->format('Y-m-d'),
                'keterangan' => 'Makan siang bersama tim',
            ]);

            Transaction::create([
                'user_id' => $budi->id,
                'category_id' => $catModels['out_Tagihan']->id,
                'tipe' => 'pengeluaran',
                'jumlah' => 350000,
                'tanggal' => Carbon::today()->subDays(1)->format('Y-m-d'),
                'keterangan' => 'Bayar tagihan listrik & wifi',
            ]);
        }
    }
}
