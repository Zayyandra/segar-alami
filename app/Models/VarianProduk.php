<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VarianProduk extends Model
{
    use HasFactory;

    protected $table = 'varian_produk';

    protected $fillable = [
        'produk_id',
        'nama_varian',
        'ukuran',
        'harga',
        'is_active',
    ];

    protected $casts = [
        'harga' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function produk(): BelongsTo
    {
        return $this->belongsTo(Produk::class);
    }

    public function konversiProduk(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(KonversiProduk::class);
    }
}
