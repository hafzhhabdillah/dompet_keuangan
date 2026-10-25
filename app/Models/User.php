<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nama',
        'email',
        'password',
        'role',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Relasi ke transaksi keuangan user.
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Relasi ke kategori custom yang dibuat user.
     */
    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }

    /**
     * Cek apakah user adalah admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Cek apakah akun user aktif.
     */
    public function isAktif(): bool
    {
        return $this->status === 'aktif';
    }

    /**
     * Hitung total pemasukan user dari database.
     */
    public function getTotalIncome(): float
    {
        return (float) $this->transactions()->where('tipe', 'pemasukan')->sum('jumlah');
    }

    /**
     * Hitung total pengeluaran user dari database.
     */
    public function getTotalExpense(): float
    {
        return (float) $this->transactions()->where('tipe', 'pengeluaran')->sum('jumlah');
    }

    /**
     * Hitung saldo saat ini (Pemasukan - Pengeluaran).
     */
    public function getBalance(): float
    {
        return $this->getTotalIncome() - $this->getTotalExpense();
    }
}
