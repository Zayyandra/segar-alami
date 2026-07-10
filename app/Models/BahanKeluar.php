<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BahanKeluar extends Model
{
    protected $table = 'bahan_keluar';

    protected $fillable = [
        'bahan_baku_id',
        'varian_produk_id',
        'user_id',
        'tanggal',
        'jumlah',
        'hasil_produksi',
        'keterangan',
    ];

    protected $casts = [
        'tanggal'        => 'date',
        'jumlah'         => 'decimal:2',
        'hasil_produksi' => 'integer',
    ];

    public function bahanBaku(): BelongsTo
    {
        return $this->belongsTo(BahanBaku::class, 'bahan_baku_id');
    }

    public function varianProduk(): BelongsTo
    {
        return $this->belongsTo(VarianProduk::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function pemakaianBatch(): HasMany
    {
        return $this->hasMany(BahanKeluarBatch::class);
    }
}
