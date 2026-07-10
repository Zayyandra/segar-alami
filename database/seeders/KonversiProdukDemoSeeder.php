<?php

namespace Database\Seeders;

use App\Models\BahanBaku;
use App\Models\KonversiProduk;
use App\Models\VarianProduk;
use Illuminate\Database\Seeder;

/**
 * PENTING: Rasio di seeder ini adalah ESTIMASI untuk keperluan demo/testing,
 * BUKAN angka pasti dari resep produksi asli UMKM Segar Alami.
 * Wajib dikoreksi manual oleh owner/admin melalui menu Konversi Produk
 * sebelum dipakai untuk perhitungan produksi/stok yang sesungguhnya.
 */
class KonversiProdukDemoSeeder extends Seeder
{
    public function run(): void
    {
        $bahan = BahanBaku::pluck('id', 'nama');

        // Format: [nama_produk, nama_varian, ukuran_atau_null, nama_bahan_baku, jumlah_per_satuan]
        $data = [
            // Susu Kedelai Botol — proporsional 2x dari gelas (0.3kg), sesuai arahan
            ['Susu Kedelai Botol', 'Original', '250ml',  'Kacang Kedelai', 0.6],
            ['Susu Kedelai Botol', 'Original', '500ml',  'Kacang Kedelai', 1.2],
            ['Susu Kedelai Botol', 'Original', '1000ml', 'Kacang Kedelai', 2.4],

            // Kembang Tahu — bahan utama sama, kedelai
            ['Kembang Tahu', 'Susu Kedelai',      null, 'Kacang Kedelai', 0.25],
            ['Kembang Tahu', 'Gula Putih/Merah',  null, 'Kacang Kedelai', 0.25],
            ['Kembang Tahu', 'Gula Merah + Jahe', null, 'Kacang Kedelai', 0.25],

            // Puding Kedelai — semua rasa pakai basis ampas/susu kedelai yang sama
            ['Puding Kedelai', 'Original', null, 'Kacang Kedelai', 0.2],

            // Pisang Bakar — bahan utama pisang, per 1 buah pisang bakar
            ['Pisang Bakar', 'Coklat',      null, 'Pisang', 0.1],
            ['Pisang Bakar', 'Keju',        null, 'Pisang', 0.1],
            ['Pisang Bakar', 'Coklat Keju', null, 'Pisang', 0.1],

            // Roti Bakar — bahan utama roti tawar
            ['Roti Bakar', 'Nanas-Stroberi', null, 'Roti Tawar', 0.5],

            // Minuman Hangat — Air Jahe & Sekoteng pakai jahe sebagai bahan utama
            ['Minuman Hangat', 'Air Jahe',  null, 'Jahe', 0.05],
            ['Minuman Hangat', 'Sekoteng',  null, 'Jahe', 0.05],
        ];

        $dibuat = 0;
        $dilewati = [];

        foreach ($data as [$namaProduk, $namaVarian, $ukuran, $namaBahan, $jumlah]) {
            $varian = VarianProduk::whereHas('produk', fn($q) => $q->where('nama', $namaProduk))
                ->where('nama_varian', $namaVarian)
                ->when($ukuran, fn($q) => $q->where('ukuran', $ukuran), fn($q) => $q->whereNull('ukuran'))
                ->first();

            $bahanBakuId = $bahan->get($namaBahan);

            if (!$varian || !$bahanBakuId) {
                $dilewati[] = "{$namaProduk} - {$namaVarian}" . ($ukuran ? " ({$ukuran})" : '') . " / {$namaBahan}";
                continue;
            }

            KonversiProduk::firstOrCreate(
                ['varian_produk_id' => $varian->id, 'bahan_baku_id' => $bahanBakuId],
                ['jumlah_per_satuan' => $jumlah]
            );
            $dibuat++;
        }

        $this->command->info("Konversi produk demo: {$dibuat} baris dibuat/sudah ada.");

        if ($dilewati) {
            $this->command->warn('Dilewati (varian/bahan tidak ditemukan): ' . implode(', ', $dilewati));
        }

        $this->command->warn(
            'PENTING: Rasio di atas adalah ESTIMASI untuk demo, bukan resep asli. '
            . 'Koreksi manual via menu Konversi Produk sebelum dipakai produksi sesungguhnya.'
        );
    }
}
