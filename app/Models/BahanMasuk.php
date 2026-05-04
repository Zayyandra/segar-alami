<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BahanMasuk extends Model
{
    protected $table = 'bahan_masuk';

    protected $fillable = [
        'bahan_baku_id',
        'user_id',
        'tanggal',
        'jumlah',
        'lead_time_hari',
        'tanggal_kadaluarsa',
        'nama_supplier',
        'keterangan',
    ];

    protected $casts = [
        'tanggal'            => 'date',
        'tanggal_kadaluarsa' => 'date',
        'jumlah'             => 'decimal:2',
    ];

    public function bahanBaku(): BelongsTo
    {
        return $this->belongsTo(BahanBaku::class, 'bahan_baku_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
