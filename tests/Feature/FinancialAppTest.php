<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class FinancialAppTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 1. Register user baru -> bisa login, role default user, status aktif, password ter-hash.
     */
    public function test_user_can_register_and_login(): void
    {
        $response = $this->post('/register', [
            'nama' => 'Test Pengguna',
            'email' => 'pengguna_baru@keuangan.test',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();

        $user = User::where('email', 'pengguna_baru@keuangan.test')->first();
        $this->assertNotNull($user);
        $this->assertEquals('user', $user->role);
        $this->assertEquals('aktif', $user->status);
        $this->assertTrue(Hash::check('secret123', $user->password));

        // Test logout & login kembali
        $this->post('/logout');
        $this->assertGuest();

        $loginResponse = $this->post('/login', [
            'email' => 'pengguna_baru@keuangan.test',
            'password' => 'secret123',
        ]);
        $loginResponse->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();
    }

    /**
     * 2. Akun nonaktif tidak bisa login.
     */
    public function test_inactive_user_cannot_login(): void
    {
        $inactiveUser = User::create([
            'nama' => 'Akun Mati',
            'email' => 'mati@keuangan.test',
            'password' => Hash::make('password123'),
            'role' => 'user',
            'status' => 'nonaktif',
        ]);

        $response = $this->post('/login', [
            'email' => 'mati@keuangan.test',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    /**
     * 3. User biasa membuka URL admin -> ditolak (403 Forbidden).
     */
    public function test_regular_user_cannot_access_admin_panel(): void
    {
        $regularUser = User::create([
            'nama' => 'User Biasa',
            'email' => 'biasa@keuangan.test',
            'password' => Hash::make('password123'),
            'role' => 'user',
            'status' => 'aktif',
        ]);

        $response = $this->actingAs($regularUser)->get('/admin/users');
        $response->assertStatus(403);
    }

    /**
     * 4. User A tidak bisa melihat/mengedit/menghapus transaksi User B (Ownership validation).
     */
    public function test_user_cannot_edit_or_delete_other_user_transaction(): void
    {
        $userA = User::create([
            'nama' => 'User A',
            'email' => 'usera@keuangan.test',
            'password' => Hash::make('password123'),
            'role' => 'user',
            'status' => 'aktif',
        ]);

        $userB = User::create([
            'nama' => 'User B',
            'email' => 'userb@keuangan.test',
            'password' => Hash::make('password123'),
            'role' => 'user',
            'status' => 'aktif',
        ]);

        $category = Category::create([
            'nama' => 'Makan',
            'tipe' => 'pengeluaran',
            'user_id' => null,
        ]);

        $trxB = Transaction::create([
            'user_id' => $userB->id,
            'category_id' => $category->id,
            'tipe' => 'pengeluaran',
            'jumlah' => 50000,
            'tanggal' => '2026-10-25',
            'keterangan' => 'Makan malam User B',
        ]);

        // User A mencoba edit transaksi milik User B
        $responseEdit = $this->actingAs($userA)->get("/transactions/{$trxB->id}/edit");
        $responseEdit->assertStatus(403);

        // User A mencoba update transaksi milik User B
        $responseUpdate = $this->actingAs($userA)->put("/transactions/{$trxB->id}", [
            'tipe' => 'pengeluaran',
            'category_id' => $category->id,
            'jumlah' => 20000,
            'tanggal' => '2026-10-25',
        ]);
        $responseUpdate->assertStatus(403);

        // User A mencoba hapus transaksi milik User B
        $responseDelete = $this->actingAs($userA)->delete("/transactions/{$trxB->id}");
        $responseDelete->assertStatus(403);
        $this->assertDatabaseHas('transactions', ['id' => $trxB->id]);
    }

    /**
     * 5. Isi saldo menambah saldo dan total pemasukan.
     */
    public function test_topup_balance_increases_income_and_balance(): void
    {
        $user = User::create([
            'nama' => 'User Topup',
            'email' => 'topup@keuangan.test',
            'password' => Hash::make('password123'),
            'role' => 'user',
            'status' => 'aktif',
        ]);

        $response = $this->actingAs($user)->post('/topup', [
            'jumlah' => 500000,
            'tanggal' => '2026-10-25',
            'keterangan' => 'Top Up Saldo Test',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertEquals(500000, $user->getTotalIncome());
        $this->assertEquals(500000, $user->getBalance());
    }

    /**
     * 6. Cegah pengeluaran yang membuat saldo negatif.
     */
    public function test_cannot_spend_more_than_current_balance(): void
    {
        $user = User::create([
            'nama' => 'User Hemat',
            'email' => 'hemat@keuangan.test',
            'password' => Hash::make('password123'),
            'role' => 'user',
            'status' => 'aktif',
        ]);

        $category = Category::create([
            'nama' => 'Belanja Barang',
            'tipe' => 'pengeluaran',
            'user_id' => null,
        ]);

        // User memiliki saldo 0, coba buat pengeluaran 100.000
        $response = $this->actingAs($user)->post('/transactions', [
            'tipe' => 'pengeluaran',
            'category_id' => $category->id,
            'jumlah' => 100000,
            'tanggal' => '2026-10-25',
        ]);

        $response->assertSessionHasErrors('jumlah');
        $this->assertEquals(0, $user->transactions()->count());
    }

    /**
     * 7. Kategori buatan user tidak muncul di akun user lain.
     */
    public function test_custom_category_isolated_to_owner(): void
    {
        $userA = User::create([
            'nama' => 'User A Kat',
            'email' => 'userakat@keuangan.test',
            'password' => Hash::make('password123'),
            'role' => 'user',
            'status' => 'aktif',
        ]);

        $userB = User::create([
            'nama' => 'User B Kat',
            'email' => 'userbkat@keuangan.test',
            'password' => Hash::make('password123'),
            'role' => 'user',
            'status' => 'aktif',
        ]);

        $customCatA = Category::create([
            'nama' => 'Rahasia Bisnis User A',
            'tipe' => 'pemasukan',
            'user_id' => $userA->id,
        ]);

        // User B membuka halaman kategori
        $response = $this->actingAs($userB)->get('/categories');
        $response->assertStatus(200);
        $response->assertDontSee('Rahasia Bisnis User A');

        // User B mencoba memakai kategori User A untuk transaksinya -> harus ditolak
        $trxResponse = $this->actingAs($userB)->post('/transactions', [
            'tipe' => 'pemasukan',
            'category_id' => $customCatA->id,
            'jumlah' => 100000,
            'tanggal' => '2026-10-25',
        ]);
        $trxResponse->assertSessionHasErrors('category_id');
    }

    /**
     * 8. Admin bisa melihat daftar user dan toggle status / hapus user.
     */
    public function test_admin_can_manage_users(): void
    {
        $admin = User::create([
            'nama' => 'Admin Boss',
            'email' => 'boss@keuangan.test',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'status' => 'aktif',
        ]);

        $targetUser = User::create([
            'nama' => 'Target User',
            'email' => 'target@keuangan.test',
            'password' => Hash::make('password123'),
            'role' => 'user',
            'status' => 'aktif',
        ]);

        // Admin lihat list
        $response = $this->actingAs($admin)->get('/admin/users');
        $response->assertStatus(200);
        $response->assertSee('Target User');

        // Admin toggle status jadi nonaktif
        $toggleResponse = $this->actingAs($admin)->patch("/admin/users/{$targetUser->id}/toggle-status");
        $toggleResponse->assertRedirect();
        $this->assertEquals('nonaktif', $targetUser->fresh()->status);

        // Admin hapus user
        $deleteResponse = $this->actingAs($admin)->delete("/admin/users/{$targetUser->id}");
        $deleteResponse->assertRedirect();
        $this->assertNull(User::find($targetUser->id));
    }
}
