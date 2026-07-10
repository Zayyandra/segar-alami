<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BahanMasuk extends Model
{
    protected $table = 'bahan_masuk';

    protected $fillable = [
        'bahan_baku_id',
        'kode_batch',
        'user_id',
        'tanggal',
        'jumlah',
        'sisa_jumlah',
        'lead_time_hari',
        'tanggal_kadaluarsa',
        'nama_supplier',
        'keterangan',
    ];

    protected $casts = [
        'tanggal'            => 'date',
        'tanggal_kadaluarsa' => 'date',
        'jumlah'             => 'decimal:2',
        'sisa_jumlah'        => 'decimal:2',
    ];

    public function bahanBaku(): BelongsTo
    {
        return $this->belongsTo(BahanBaku::class, 'bahan_baku_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function pemakaianBatch(): HasMany
    {
        return $this->hasMany(BahanKeluarBatch::class);
    }

    /**
     * Ambil batch-batch bahan baku tertentu yang masih punya sisa stok,
     * diurutkan FEFO: tanggal_kadaluarsa paling dekat duluan.
     * Batch tanpa tanggal_kadaluarsa ditaruh paling akhir.
     */
    public static function batchTersediaFefo(int $bahanBakuId)
    {
        return self::where('bahan_baku_id', $bahanBakuId)
            ->where('sisa_jumlah', '>', 0)
            ->orderByRaw('CASE WHEN tanggal_kadaluarsa IS NULL THEN 1 ELSE 0 END')
            ->orderBy('tanggal_kadaluarsa', 'asc')
            ->orderBy('tanggal', 'asc')
            ->get();
    }
}
