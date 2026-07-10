<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BahanKeluarBatch extends Model
{
    protected $table = 'bahan_keluar_batch';

    protected $fillable = [
        'bahan_keluar_id',
        'bahan_masuk_id',
        'jumlah_diambil',
    ];

    protected $casts = [
        'jumlah_diambil' => 'decimal:2',
    ];

    public function bahanKeluar(): BelongsTo
    {
        return $this->belongsTo(BahanKeluar::class);
    }

    public function bahanMasuk(): BelongsTo
    {
        return $this->belongsTo(BahanMasuk::class);
    }
}
