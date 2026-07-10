<?php

namespace Database\Seeders;

use App\Models\Produk;
use App\Models\VarianProduk;
use Illuminate\Database\Seeder;

class DummyStokSeeder extends Seeder
{
    /**
     * Nama produk yang REAL diproduksi manual di lokasi — stok TIDAK di-dummy,
     * biar tetap murni dari alur Bahan Keluar -> Konversi Produk yang sebenarnya.
     */
    private const PRODUK_EXCLUDE = [
        'Susu Kedelai Gelas',
        'Susu Kedelai Botol',
        'Kembang Tahu',
        'Puding Kedelai',
        'Pisang Bakar',
        'Roti Bakar',
        'Minuman Hangat',
    ];

    public function run(): void
    {
        $produkIdExclude = Produk::whereIn('nama', self::PRODUK_EXCLUDE)->pluck('id');

        $variants = VarianProduk::whereNotIn('produk_id', $produkIdExclude)->get();

        if ($variants->isEmpty()) {
            $this->command->warn(
                'Tidak ada varian produk di luar daftar exclude. '
                . 'Semua produk saat ini termasuk kategori "diproduksi manual di lokasi" — tidak ada yang di-dummy.'
            );
            return;
        }

        foreach ($variants as $varian) {
            $varian->update(['stok' => random_int(5, 100)]);
        }

        $this->command->info("Dummy stok berhasil diisi untuk {$variants->count()} varian produk (produk pabrik).");
    }
}
