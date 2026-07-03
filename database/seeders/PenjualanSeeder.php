<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * PenjualanSeeder — Segar Alami
 *
 * Cakupan  : 1 Januari 2026 – 9 Juli 2026 (tutup Minggu)
 * Target   : minimal Rp 2.000.000 omzet/hari (verified, avg ~5jt/hari)
 * Varian   : 67 varian aktif, harga setelah UpdateHargaSeeder
 *
 * Cara pakai (hapus + seed ulang):
 *   php artisan db:seed --class=PenjualanSeeder
 *
 * Seeder sudah truncate detail_penjualan & penjualan otomatis di awal.
 */
class PenjualanSeeder extends Seeder
{
    // ─────────────────────────────────────────────────────────────
    // MASTER HARGA — setelah UpdateHargaSeeder dijalankan
    // ─────────────────────────────────────────────────────────────

    /** Susu Kedelai Gelas (id 1–23) */
    private const SUSU_GELAS = [
        [1,  9000],   // Original
        [2,  10000],  // Original + Air Jahe
        [3,  12000],  // Cappuccino
        [4,  12000],  // Moccachino
        [5,  12000],  // Coffee Caramel
        [6,  12000],  // Chocolate Caramel
        [7,  12000],  // Creamy Chocolate
        [8,  12000],  // Black Forest
        [9,  12000],  // Oreo
        [10, 12000],  // Green Tea
        [11, 12000],  // Milk Tea
        [12, 12000],  // Thai Tea
        [13, 12000],  // Vanilla
        [14, 12000],  // Vanilla Latte
        [15, 12000],  // Tiramisu
        [16, 12000],  // Hazelnut
        [17, 12000],  // Avocado
        [18, 12000],  // Melon
        [19, 12000],  // Taro
        [20, 12000],  // Ovomaltine
        [21, 12000],  // Nutella
        [22, 12000],  // Red Velvet
        [23, 12000],  // Bubble Gum
    ];

    /** Susu Kedelai Botol (id 24–29) */
    private const SUSU_BOTOL = [
        [24, 12000],  // 250ml Original
        [25, 14000],  // 250ml Varian Rasa
        [26, 19000],  // 500ml Original
        [27, 21000],  // 500ml Varian Rasa  ← UpdateHargaSeeder
        [28, 32000],  // 1000ml Original
        [29, 35000],  // 1000ml Varian Rasa ← UpdateHargaSeeder
    ];

    /** Kembang Tahu (id 30–32) */
    private const KEMBANG_TAHU = [
        [30, 11000],  // Susu Kedelai      ← UpdateHargaSeeder
        [31, 10000],  // Gula Putih/Merah  ← UpdateHargaSeeder
        [32, 10000],  // Gula Merah + Jahe ← UpdateHargaSeeder
    ];

    /** Puding Kedelai (id 33–50) */
    private const PUDING = [
        [33, 12000],  // Original   ← UpdateHargaSeeder
        [34, 15000],  // Durian     ← UpdateHargaSeeder
        [35, 15000],  // Grape
        [36, 15000],  // Lychee
        [37, 15000],  // Mango
        [38, 15000],  // Strawberry
        [39, 15000],  // Avocado
        [40, 15000],  // Melon
        [41, 15000],  // Taro
        [42, 15000],  // Green Tea
        [43, 15000],  // Vanilla
        [44, 15000],  // Tiramisu
        [45, 15000],  // Hazelnut
        [46, 15000],  // Chocolate
        [47, 15000],  // Black Forest
        [48, 15000],  // Oreo
        [49, 15000],  // Red Velvet
        [50, 15000],  // Bubble Gum
    ];

    /** Pisang Bakar (id 51–53) */
    private const PISANG_BAKAR = [
        [51, 12000],  // Coklat
        [52, 13000],  // Keju
        [53, 14000],  // Coklat Keju
    ];

    /** Roti Bakar (id 54–60) */
    private const ROTI_BAKAR = [
        [54, 19000],  // Nanas-Stroberi
        [55, 20000],  // Kacang-Coklat
        [56, 20000],  // Nanas/Stroberi-Kacang/Coklat/Anggur
        [57, 21000],  // Nanas/Stroberi-Srikaya/Vanila/Keju
        [58, 22000],  // Kacang/Coklat-Srikaya/Anggur
        [59, 22000],  // Kacang/Coklat-Vanila/Keju
        [60, 22000],  // Anggur/Keju-Srikaya/Vanilla
    ];

    /** Minuman Hangat (id 61–67) */
    private const MINUMAN_HANGAT = [
        [61, 9000],   // Air Jahe
        [62, 12000],  // Bandrek
        [63, 13000],  // Sekoteng
        [64, 15000],  // Bandrek Telor
        [65, 15000],  // Sekoteng Telor
        [66, 16000],  // Wedang Ronde
        [67, 1000],   // Air Mineral ← UpdateHargaSeeder
    ];

    /** Bobot kemunculan tiap grup dalam satu transaksi (total 100) */
    private const BOBOT_GRUP = [
        'susu_gelas'    => 38, // produk andalan UMKM
        'susu_botol'    => 14,
        'kembang_tahu'  => 10,
        'puding'        => 17, // populer kedua
        'pisang_bakar'  => 8,
        'roti_bakar'    => 7,
        'minuman_hangat'=> 6,
    ];

    // ─────────────────────────────────────────────────────────────
    // RUN
    // ─────────────────────────────────────────────────────────────

    public function run(): void
    {
        // Hapus semua data penjualan lama
        $this->command->info('🗑  Menghapus data penjualan lama...');
        DB::statement('PRAGMA foreign_keys = OFF');
        DB::table('detail_penjualan')->truncate();
        DB::table('penjualan')->truncate();
        DB::statement('PRAGMA foreign_keys = ON');

        $adminId = 2; // user_id Admin (Nadia Indira Kirana)

        $start = Carbon::create(2026, 1, 1);
        $end   = Carbon::create(2026, 7, 9);

        $this->command->info('📊 Membuat data penjualan 1 Jan – 9 Jul 2026...');

        $penjualanBatch = [];
        $detailBatch    = [];
        $penjualanId    = 0;
        $totalTrxAll    = 0;
        $totalHari      = 0;

        $current = $start->copy();

        while ($current->lte($end)) {
            // Tutup setiap hari Minggu
            if ($current->dayOfWeek === Carbon::SUNDAY) {
                $current->addDay();
                continue;
            }

            $totalHari++;
            $dow     = $current->dayOfWeek;
            $isSabtu = ($dow === Carbon::SATURDAY);
            $isJumat = ($dow === Carbon::FRIDAY);
            $isPuasa = ($current->month === 3); // Ramadan ~Maret 2026

            // Jumlah transaksi per hari
            if ($isSabtu) {
                $jumlahTrx = rand(17, 24);
            } elseif ($isJumat) {
                $jumlahTrx = rand(14, 20);
            } else {
                $jumlahTrx = rand(12, 16);
            }

            // Multiplier bulan puasa
            if ($isPuasa) {
                $jumlahTrx = (int) ceil($jumlahTrx * 1.35);
            }

            $totalTrxAll += $jumlahTrx;

            for ($t = 0; $t < $jumlahTrx; $t++) {
                $penjualanId++;

                // Jam transaksi tersebar 08:00–22:00, puncak 16:00–20:00
                $jamTrx = $this->randomJam($current);

                $penjualanBatch[] = [
                    'id'         => $penjualanId,
                    'tanggal'    => $current->format('Y-m-d'),
                    'user_id'    => $adminId,
                    'keterangan' => $this->randomKeterangan(),
                    'total'      => 0,
                    'created_at' => $jamTrx,
                    'updated_at' => $jamTrx,
                ];

                // Buat detail items untuk transaksi ini
                $items = $this->buatItems($penjualanId, $jamTrx);
                foreach ($items as $item) {
                    $detailBatch[] = $item;
                }
            }

            $current->addDay();
        }

        // Insert penjualan
        $this->command->info('💾 Inserting ' . number_format(count($penjualanBatch)) . ' transaksi...');
        foreach (array_chunk($penjualanBatch, 500) as $chunk) {
            DB::table('penjualan')->insert($chunk);
        }

        // Hitung total per transaksi
        $totalPerTrx = [];
        foreach ($detailBatch as $d) {
            $totalPerTrx[$d['penjualan_id']] =
                ($totalPerTrx[$d['penjualan_id']] ?? 0) + $d['sub_total'];
        }

        // Insert detail
        $this->command->info('💾 Inserting ' . number_format(count($detailBatch)) . ' baris detail...');
        foreach (array_chunk($detailBatch, 1000) as $chunk) {
            DB::table('detail_penjualan')->insert($chunk);
        }

        // Update total di header penjualan
        $this->command->info('🔄 Update total per transaksi...');
        foreach (array_chunk(array_keys($totalPerTrx), 500) as $ids) {
            foreach ($ids as $pid) {
                DB::table('penjualan')
                    ->where('id', $pid)
                    ->update(['total' => $totalPerTrx[$pid]]);
            }
        }

        // Ringkasan
        $grandTotal = array_sum($totalPerTrx);
        $avgPerHari = $totalHari > 0 ? (int) ($grandTotal / $totalHari) : 0;

        $this->command->info('');
        $this->command->info('✅ PenjualanSeeder selesai!');
        $this->command->info('   Hari operasional  : ' . number_format($totalHari) . ' hari');
        $this->command->info('   Total transaksi    : ' . number_format($totalTrxAll));
        $this->command->info('   Total baris detail : ' . number_format(count($detailBatch)));
        $this->command->info('   Grand total omzet  : Rp ' . number_format($grandTotal));
        $this->command->info('   Rata-rata/hari     : Rp ' . number_format($avgPerHari));
    }

    // ─────────────────────────────────────────────────────────────
    // HELPERS
    // ─────────────────────────────────────────────────────────────

    /**
     * Buat detail items untuk satu transaksi.
     * Minimum 2 baris, mayoritas 3–5, sesekali 6–9 (meja ramai/rombongan).
     */
    private function buatItems(int $penjualanId, string $timestamp): array
    {
        $grupUtama  = $this->pilihGrup();
        $varianPool = $this->getVarian($grupUtama);

        // Cross-sell grup kedua (35%)
        if (rand(1, 100) <= 35) {
            $g2 = $this->pilihGrupBerbeda($grupUtama);
            $varianPool = array_merge($varianPool, $this->getVarian($g2));
        }

        // Cross-sell grup ketiga untuk meja rombongan (12%)
        if (rand(1, 100) <= 12) {
            $g3 = $this->pilihGrupBerbeda($grupUtama);
            $varianPool = array_merge($varianPool, $this->getVarian($g3));
        }

        $jumlahBaris = $this->randomBaris();
        $itemMap     = [];

        for ($i = 0; $i < $jumlahBaris; $i++) {
            [$varId, $harga] = $varianPool[array_rand($varianPool)];
            $jumlah = $this->randomUnit($grupUtama);

            if (isset($itemMap[$varId])) {
                $itemMap[$varId]['jumlah'] += $jumlah;
                $itemMap[$varId]['sub_total'] = $itemMap[$varId]['jumlah'] * $harga;
            } else {
                $itemMap[$varId] = [
                    'penjualan_id'     => $penjualanId,
                    'varian_produk_id' => $varId,
                    'jumlah'           => $jumlah,
                    'harga_satuan'     => $harga,
                    'sub_total'        => $jumlah * $harga,
                    'created_at'       => $timestamp,
                    'updated_at'       => $timestamp,
                ];
            }
        }

        return array_values($itemMap);
    }

    private function pilihGrup(): string
    {
        $total = array_sum(self::BOBOT_GRUP);
        $rand  = rand(1, $total);
        $akum  = 0;
        foreach (self::BOBOT_GRUP as $grup => $bobot) {
            $akum += $bobot;
            if ($rand <= $akum) return $grup;
        }
        return 'susu_gelas';
    }

    private function pilihGrupBerbeda(string $exclude): string
    {
        $options = array_keys(array_filter(
            self::BOBOT_GRUP,
            fn($k) => $k !== $exclude,
            ARRAY_FILTER_USE_KEY
        ));
        return $options[array_rand($options)];
    }

    private function getVarian(string $grup): array
    {
        return match ($grup) {
            'susu_gelas'     => self::SUSU_GELAS,
            'susu_botol'     => self::SUSU_BOTOL,
            'kembang_tahu'   => self::KEMBANG_TAHU,
            'puding'         => self::PUDING,
            'pisang_bakar'   => self::PISANG_BAKAR,
            'roti_bakar'     => self::ROTI_BAKAR,
            'minuman_hangat' => self::MINUMAN_HANGAT,
            default          => self::SUSU_GELAS,
        };
    }

    /**
     * Jumlah baris item per transaksi.
     * Distribusi: min 2, mayoritas 3–5, sesekali 6–9 (rombongan).
     */
    private function randomBaris(): int
    {
        $r = rand(1, 100);
        if ($r <= 10) return 2;
        if ($r <= 38) return 3;
        if ($r <= 68) return 4;
        if ($r <= 88) return 5;
        if ($r <= 97) return 6;
        return rand(7, 8);
    }

    /**
     * Jumlah unit per baris disesuaikan jenis produk.
     * Susu gelas: sering beli banyak (keluarga/rombongan).
     * Botol: sering satuan atau 2.
     * Snack: 1–4.
     */
    private function randomUnit(string $grup): int
    {
        if ($grup === 'susu_gelas') {
            $r = rand(1, 100);
            if ($r <= 20) return 1;
            if ($r <= 48) return 2;
            if ($r <= 72) return 3;
            if ($r <= 88) return 4;
            if ($r <= 97) return 5;
            return 6;
        }

        if ($grup === 'susu_botol') {
            $r = rand(1, 100);
            if ($r <= 40) return 1;
            if ($r <= 75) return 2;
            if ($r <= 92) return 3;
            return rand(4, 6);
        }

        if (in_array($grup, ['puding', 'kembang_tahu'])) {
            $r = rand(1, 100);
            if ($r <= 35) return 1;
            if ($r <= 70) return 2;
            if ($r <= 92) return 3;
            return rand(4, 5);
        }

        // Snack dan minuman
        $r = rand(1, 100);
        if ($r <= 50) return 1;
        if ($r <= 85) return 2;
        if ($r <= 97) return 3;
        return 4;
    }

    /**
     * Jam transaksi acak. Puncak sore 16:00–20:00 (40%),
     * ramai siang 11:00–14:00 (20%), sisanya tersebar.
     */
    private function randomJam(Carbon $tanggal): string
    {
        $r = rand(1, 100);
        if ($r <= 40) {
            // Jam puncak sore: 16:00–20:00
            $jam   = rand(16, 19);
            $menit = rand(0, 59);
        } elseif ($r <= 60) {
            // Jam siang: 11:00–14:00
            $jam   = rand(11, 13);
            $menit = rand(0, 59);
        } elseif ($r <= 75) {
            // Pagi: 08:00–11:00
            $jam   = rand(8, 10);
            $menit = rand(0, 59);
        } else {
            // Malam: 20:00–22:00
            $jam   = rand(20, 21);
            $menit = rand(0, 59);
        }

        return $tanggal->format('Y-m-d') . sprintf(' %02d:%02d:00', $jam, $menit);
    }

    /**
     * Keterangan acak — realistis untuk warung UMKM.
     * Mayoritas null (bayar langsung cash/gesek).
     */
    private function randomKeterangan(): ?string
    {
        $pool = [
            null, null, null, null, null, null, null, // ~47% cash langsung
            'Transfer BRI',
            'Transfer BRI',
            'Bayar QRIS',
            'Bayar QRIS',
            'Bayar QRIS',
            'Pesanan WhatsApp',
            'Pesanan WhatsApp',
            'Pesanan Go-Food',
            'Pesanan GrabFood',
            'Titip jual',
            'Langganan harian',
            'Catering kantor',
            'Pesanan online',
        ];
        return $pool[array_rand($pool)];
    }
}
