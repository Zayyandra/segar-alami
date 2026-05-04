<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailPenjualan extends Model
{
    protected $table = 'detail_penjualan';

    protected $fillable = [
        'penjualan_id',
        'varian_produk_id',
        'jumlah',
        'harga_satuan',
        'sub_total',
    ];

    protected $casts = [
        'harga_satuan' => 'decimal:2',
        'sub_total'    => 'decimal:2',
    ];

    public function penjualan(): BelongsTo
    {
        return $this->belongsTo(Penjualan::class);
    }

    public function varianProduk(): BelongsTo
    {
        return $this->belongsTo(VarianProduk::class);
    }
}
