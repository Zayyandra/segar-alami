<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KonversiProduk extends Model
{
    protected $table = 'konversi_produk';

    protected $fillable = [
        'varian_produk_id',
        'bahan_baku_id',
        'jumlah_per_satuan',
    ];

    protected $casts = [
        'jumlah_per_satuan' => 'decimal:4',
    ];

    public function varianProduk(): BelongsTo
    {
        return $this->belongsTo(VarianProduk::class);
    }

    public function bahanBaku(): BelongsTo
    {
        return $this->belongsTo(BahanBaku::class);
    }
}
