<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BahanBaku extends Model
{
    use HasFactory;

    protected $table = 'bahan_baku';

    protected $fillable = [
        'nama', 'satuan', 'stok_saat_ini', 'stok_minimum',
        'harga_per_satuan', 'kategori_bb', 'tracking_ss_rop', 'is_active',
    ];

    protected $casts = [
        'stok_saat_ini' => 'decimal:2',
        'stok_minimum' => 'decimal:2',
        'harga_per_satuan' => 'decimal:2',
        'tracking_ss_rop' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function bahanMasuk(): HasMany
    {
        return $this->hasMany(BahanMasuk::class, 'bahan_baku_id');
    }

    public function bahanKeluar(): HasMany
    {
        return $this->hasMany(BahanKeluar::class, 'bahan_baku_id');
    }

    public function konversiProduk(): HasMany
    {
        return $this->hasMany(KonversiProduk::class, 'bahan_baku_id');
    }

    public function getStatusStokAttribute(): string
    {
        if ($this->stok_minimum <= 0) {
            return 'unknown';
        }

        return $this->stok_saat_ini <= $this->stok_minimum ? 'kritis' : 'aman';
    }

    public function getNilaiStokAttribute(): float
    {
        return $this->stok_saat_ini * $this->harga_per_satuan;
    }

    public function hitungSsRop(): array
    {
        $pemakaianPerHari = $this->bahanKeluar()
            ->selectRaw('DATE(tanggal) as tgl, SUM(jumlah) as total')
            ->groupBy('tgl')
            ->pluck('total');

        if ($pemakaianPerHari->isEmpty()) {
            return ['ss' => null, 'rop' => null, 'D' => null, 'Dmax' => null, 'L' => null, 'Lmax' => null];
        }

        $D = round($pemakaianPerHari->avg(), 2);
        $Dmax = round($pemakaianPerHari->max(), 2);

        $leadTimes = $this->bahanMasuk()
            ->whereNotNull('lead_time_hari')
            ->pluck('lead_time_hari');

        $L = $leadTimes->isEmpty() ? 1 : round($leadTimes->avg(), 2);
        $Lmax = $leadTimes->isEmpty() ? 1 : $leadTimes->max();

        $ss = round(($Dmax * $Lmax) - ($D * $L), 2);
        $rop = round(($D * $L) + $ss, 2);

        return compact('ss', 'rop', 'D', 'Dmax', 'L', 'Lmax');
    }
}
