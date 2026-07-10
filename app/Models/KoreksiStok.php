<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KoreksiStok extends Model
{
    protected $table = 'koreksi_stok';

    protected $fillable = [
        'bahan_baku_id',
        'user_id',
        'stok_sebelum',
        'stok_sesudah',
        'selisih',
        'alasan',
        'keterangan',
    ];

    protected $casts = [
        'stok_sebelum' => 'decimal:2',
        'stok_sesudah' => 'decimal:2',
        'selisih'      => 'decimal:2',
    ];

    public function bahanBaku(): BelongsTo
    {
        return $this->belongsTo(BahanBaku::class, 'bahan_baku_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public const ALASAN_OPTIONS = [
        'stok_opname'       => 'Stok Opname (Perbedaan Fisik vs Sistem)',
        'bahan_rusak'       => 'Bahan Rusak / Kadaluarsa Tidak Tercatat',
        'kesalahan_input'   => 'Kesalahan Input Sebelumnya',
        'lainnya'           => 'Lainnya',
    ];
}
