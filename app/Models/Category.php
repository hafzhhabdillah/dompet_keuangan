<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nama',
        'tipe',
        'user_id',
    ];

    /**
     * Relasi ke user pemilik kategori (null jika kategori bawaan).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke transaksi yang menggunakan kategori ini.
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Cek apakah ini kategori bawaan sistem.
     */
    public function isDefault(): bool
    {
        return $this->user_id === null;
    }

    /**
     * Scope untuk mengambil kategori bawaan + kategori milik user yang sedang login.
     */
    public function scopeAvailableForUser(Builder $query, int $userId): Builder
    {
        return $query->where(function ($q) use ($userId) {
            $q->whereNull('user_id')
                ->orWhere('user_id', $userId);
        });
    }
}
