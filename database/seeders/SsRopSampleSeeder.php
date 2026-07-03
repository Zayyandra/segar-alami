<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * Seeder data sampel untuk perhitungan Safety Stock & ROP.
 * Mengisi bahan_keluar (pemakaian harian) dan lead_time_hari di bahan_masuk
 * agar metode SS/ROP dapat didemonstrasikan dengan data realistis.
 *
 * Data ini merupakan DATA AWAL/SAMPEL untuk mendemonstrasikan perhitungan,
 * bukan klaim data operasional historis.
 */
class SsRopSampleSeeder extends Seeder
{
    public function run(): void
    {
        $adminId = DB::table('users')->where('name', 'like', '%Admin%')->value('id')
            ?? DB::table('users')->min('id');

        // Pola pemakaian harian per bahan baku (id => array jumlah per hari, 14 hari).
        // Nilai max tiap array = Dmax, rata-rata = D. Dikalibrasi agar ROP wajar.
        $pemakaian = [
            1 => [3, 4, 2, 5, 3, 3, 4, 2, 3, 4, 3, 5, 2, 3],   // Kacang Kedelai
            2 => [2, 3, 1, 2, 2, 3, 2, 1, 2, 3, 2, 2, 1, 2],   // Gula Pasir
            3 => [1, 2, 1, 1, 2, 1, 1, 2, 1, 1, 2, 1, 1, 1],   // Daun Pandan
            4 => [1, 0, 1, 1, 0, 1, 1, 0, 1, 1, 0, 1, 1, 0],   // Tepung Biang
            5 => [0, 1, 0, 1, 0, 0, 1, 0, 1, 0, 0, 1, 0, 0],   // Soka
        ];

        // Lead time (hari) per bahan: beberapa kali pengadaan. max = Lmax, avg = L.
        $leadTimes = [
            1 => [2, 3, 2],   // Kacang Kedelai: avg ~2.33, max 3
            2 => [2, 3, 2],   // Gula Pasir
            3 => [1, 2, 1],   // Daun Pandan
            4 => [2, 3, 2],   // Tepung Biang
            5 => [2, 3, 2],   // Soka
        ];

        $keluarRows = [];
        foreach ($pemakaian as $bahanId => $harian) {
            foreach ($harian as $i => $jumlah) {
                if ($jumlah <= 0) continue;
                $tgl = Carbon::today()->subDays(14 - $i)->toDateString();
                $keluarRows[] = [
                    'bahan_baku_id' => $bahanId,
                    'user_id'       => $adminId,
                    'tanggal'       => $tgl,
                    'jumlah'        => $jumlah,
                    'keterangan'    => 'Pemakaian produksi (data sampel)',
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ];
            }
        }

        $masukRows = [];
        foreach ($leadTimes as $bahanId => $lts) {
            foreach ($lts as $j => $lt) {
                $tgl = Carbon::today()->subDays(30 - ($j * 10))->toDateString();
                $masukRows[] = [
                    'bahan_baku_id'      => $bahanId,
                    'user_id'            => $adminId,
                    'tanggal'            => $tgl,
                    'jumlah'             => 10,
                    'lead_time_hari'     => $lt,
                    'tanggal_kadaluarsa' => Carbon::today()->addDays(20 + $j)->toDateString(),
                    'nama_supplier'      => 'Supplier Lokal',
                    'keterangan'         => 'Pengadaan (data sampel)',
                    'created_at'         => now(),
                    'updated_at'         => now(),
                ];
            }
        }

        DB::table('bahan_keluar')->insert($keluarRows);
        DB::table('bahan_masuk')->insert($masukRows);

        $this->command->info('Data sampel SS/ROP berhasil: '
            . count($keluarRows) . ' bahan keluar, '
            . count($masukRows) . ' bahan masuk.');
    }
}
