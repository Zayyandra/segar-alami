<?php
namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PersediaanSeeder extends Seeder
{
    public function run(): void
    {
        mt_srand(42);

        $this->command->info('🗑  Menghapus data persediaan lama...');
        DB::statement('PRAGMA foreign_keys = OFF');
        DB::table('bahan_masuk')->truncate();
        DB::table('bahan_keluar')->truncate();
        DB::statement('PRAGMA foreign_keys = ON');

        $adminId = 2;

        $this->seedBahanMasuk($adminId);
        $this->seedBahanKeluar($adminId);
        $this->fixStokDanKategori();

        $masukCount  = DB::table('bahan_masuk')->count();
        $keluarCount = DB::table('bahan_keluar')->count();
        $this->command->info("PersediaanSeeder selesai: {$masukCount} bahan masuk, {$keluarCount} bahan keluar.");
    }

    private function seedBahanMasuk(int $adminId): void
    {
        $restokJadwal = [
            [1, 40, 50, 10, 2, 'Toko Bahan Pokok Pekanbaru', 60],
            [2, 15, 20, 10, 1, 'Toko Bahan Pokok Pekanbaru', 180],
            // id 3 (Daun Pandan) dihapus
            [4, 2, 3, 20, 3, 'Supplier Bahan Kue', 180],
            [5, 4, 5, 20, 3, 'Supplier Bahan Kue', 180],
        ];

        $data  = [];
        $start = Carbon::create(2026, 1, 1);
        $end   = Carbon::now()->subDays(2);

        foreach ($restokJadwal as [$bbId, $min, $max, $frekuensi, $leadTime, $supplier, $shelfLife]) {
            $current = $start->copy();
            while ($current->lte($end)) {
                if ($current->dayOfWeek === 0) { $current->addDay(); continue; }

                $jumlah = round($min + ($max - $min) * (mt_rand(0, 100) / 100), 2);

                $data[] = [
                    'bahan_baku_id'      => $bbId,
                    'user_id'            => $adminId,
                    'tanggal'            => $current->format('Y-m-d'),
                    'jumlah'             => $jumlah,
                    'lead_time_hari'     => $leadTime,
                    'tanggal_kadaluarsa' => $current->copy()->addDays($shelfLife)->format('Y-m-d'),
                    'nama_supplier'      => $supplier,
                    'keterangan'         => null,
                    'created_at'         => $current->format('Y-m-d') . ' 07:30:00',
                    'updated_at'         => $current->format('Y-m-d') . ' 07:30:00',
                ];

                $current->addDays($frekuensi);
            }
        }

        DB::table('bahan_masuk')->insert($data);
        $this->overrideExpiryUntukDemo();
    }

    private function overrideExpiryUntukDemo(): void
    {
        $tahun = Carbon::now()->year;

        $targetTanggal = [
            Carbon::create($tahun, 7, 8)->format('Y-m-d'),
            Carbon::create($tahun, 7, 9)->format('Y-m-d'),
            Carbon::create($tahun, 7, 10)->format('Y-m-d'),
        ];

        $safeExpiry = Carbon::create($tahun, 7, 31)->format('Y-m-d');

        foreach ([1, 2, 4, 5] as $bbId) { // id 3 dihapus
            DB::table('bahan_masuk')
                ->where('bahan_baku_id', $bbId)
                ->update(['tanggal_kadaluarsa' => $safeExpiry]);

            $batchTerbaru = DB::table('bahan_masuk')
                ->where('bahan_baku_id', $bbId)
                ->orderByDesc('tanggal')
                ->orderByDesc('id')
                ->limit(3)
                ->pluck('id');

            foreach ($batchTerbaru as $i => $id) {
                if (!isset($targetTanggal[$i])) continue;
                DB::table('bahan_masuk')
                    ->where('id', $id)
                    ->update(['tanggal_kadaluarsa' => $targetTanggal[$i]]);
            }
        }

        $this->command->info('📅 Semua batch → 31 Juli, 3 terbaru per bahan → 8/9/10 Juli.');
    }

    private function seedBahanKeluar(int $adminId): void
    {
        $pakaiHarian = [
            [1, 2.0, 4.0],   // Kacang Kedelai
            [2, 0.5, 1.5],   // Gula Pasir
            // id 3 (Daun Pandan) dihapus
            [4, 0.05, 0.10], // Tepung Biang
            [5, 0.10, 0.20], // Soka
        ];

        $data    = [];
        $start   = Carbon::create(2026, 1, 1);
        $end     = Carbon::now()->subDays(2);
        $current = $start->copy();

        while ($current->lte($end)) {
            if ($current->dayOfWeek === 0) { $current->addDay(); continue; }

            $multiplier = $current->dayOfWeek === 6 ? 1.4 : 1.0;
            if ($current->month === 3) $multiplier *= 1.2;

            foreach ($pakaiHarian as [$bbId, $min, $max]) {
                $jumlah = round(($min + ($max - $min) * (mt_rand(0, 100) / 100)) * $multiplier, 3);

                $data[] = [
                    'bahan_baku_id' => $bbId,
                    'user_id'       => $adminId,
                    'tanggal'       => $current->format('Y-m-d'),
                    'jumlah'        => $jumlah,
                    'keterangan'    => null,
                    'created_at'    => $current->format('Y-m-d') . ' 17:00:00',
                    'updated_at'    => $current->format('Y-m-d') . ' 17:00:00',
                ];
            }

            $current->addDay();
        }

        foreach (array_chunk($data, 500) as $chunk) {
            DB::table('bahan_keluar')->insert($chunk);
        }
    }

    private function fixStokDanKategori(): void
    {
        // Stok saat ini untuk 4 bahan utama
        $stok = [
            1 => 30, // Kacang Kedelai
            2 => 20, // Gula Pasir
            4 => 5,  // Tepung Biang
            5 => 3,  // Soka
        ];

        foreach ($stok as $id => $nilai) {
            DB::table('bahan_baku')->where('id', $id)->update(['stok_saat_ini' => $nilai]);
        }

        // Daun Pandan → pendukung + nonaktif dari SS/ROP tracking
        DB::table('bahan_baku')->where('id', 3)->update([
            'kategori_bb' => 'pendukung',
            'is_active'   => false,
        ]);

        $this->command->info('📦 Stok disesuaikan, Daun Pandan dipindah ke pendukung & dinonaktifkan.');
    }
}
