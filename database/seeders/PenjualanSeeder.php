<?php

namespace Database\Seeders;

use App\Models\Penjualan;
use App\Models\VarianProduk;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PenjualanSeeder extends Seeder
{
    public function run(): void
    {
        $user = DB::table('users')->first();
        if (!$user) {
            $this->command->error('Tidak ada user. Jalankan RolePermissionSeeder dulu.');
            return;
        }

        $varians = VarianProduk::where('is_active', true)->get()->keyBy('id');
        if ($varians->isEmpty()) {
            $this->command->error('Tidak ada varian produk. Jalankan SegarAlamiSeeder dulu.');
            return;
        }

        $buatTransaksi = function (string $tanggal, array $items, ?string $keterangan = null) use ($user) {
            $total = collect($items)->sum(fn($i) => $i['harga_satuan'] * $i['jumlah']);

            $penjualan = Penjualan::create([
                'user_id'    => $user->id,
                'tanggal'    => $tanggal,
                'keterangan' => $keterangan,
                'total'      => $total,
            ]);

            foreach ($items as $item) {
                DB::table('detail_penjualan')->insert([
                    'penjualan_id'     => $penjualan->id,
                    'varian_produk_id' => $item['varian_produk_id'],
                    'jumlah'           => $item['jumlah'],
                    'harga_satuan'     => $item['harga_satuan'],
                    'sub_total'        => $item['harga_satuan'] * $item['jumlah'],
                    'created_at'       => now(),
                    'updated_at'       => now(),
                ]);
            }
        };

        $cari = function (string $namaVarian, ?string $ukuran = null) use ($varians): ?VarianProduk {
            return $varians->first(function ($v) use ($namaVarian, $ukuran) {
                return $v->nama_varian === $namaVarian
                    && ($ukuran === null || $v->ukuran === $ukuran);
            });
        };

        $item = fn(?VarianProduk $v, int $jumlah) => $v ? [
            'varian_produk_id' => $v->id,
            'jumlah'           => $jumlah,
            'harga_satuan'     => $v->harga,
        ] : null;

        $f = fn(array $items) => array_values(array_filter($items));

        // Referensi varian
        $susuOriginal     = $cari('Original');
        $susuCappuccino   = $cari('Cappuccino');
        $susuThai         = $cari('Thai Tea');
        $susuOreo         = $cari('Oreo');
        $susuGreenTea     = $cari('Green Tea');
        $susuChoco        = $cari('Creamy Chocolate');

        $botol250ori      = $cari('Original', '250ml');
        $botol250rasa     = $cari('Varian Rasa', '250ml');
        $botol500ori      = $cari('Original', '500ml');
        $botol500rasa     = $cari('Varian Rasa', '500ml');
        $botol1000ori     = $cari('Original', '1000ml');

        $kembangSusu      = $cari('Susu Kedelai');
        $kembangGula      = $cari('Gula Putih/Merah');
        $kembangJahe      = $cari('Gula Merah + Jahe');

        $pudingChoco      = $cari('Chocolate');
        $pudingStraw      = $cari('Strawberry');
        $pudingTaro       = $cari('Taro');
        $pudingDurian     = $cari('Durian');

        $pisangCoklat     = $cari('Coklat');
        $pisangKeju       = $cari('Keju');
        $pisangCoklatKeju = $cari('Coklat Keju');

        $rotiNanasStraw   = $cari('Nanas-Stroberi');
        $rotiKacangCoklat = $cari('Kacang-Coklat');

        $bandrek          = $cari('Bandrek');
        $sekoteng         = $cari('Sekoteng');
        $wedangRonde      = $cari('Wedang Ronde');
        $airJahe          = $cari('Air Jahe');
        $airMineral       = $cari('Air Mineral');

        // ========================
        // FEBRUARI 2026
        // ========================
        $buatTransaksi('2026-02-03', $f([
            $item($susuOriginal, 4),
            $item($kembangSusu, 2),
            $item($airMineral, 2),
        ]), 'Pelanggan reguler pagi');

        $buatTransaksi('2026-02-05', $f([
            $item($botol500ori, 3),
            $item($botol250rasa, 2),
            $item($pudingChoco, 3),
        ]));

        $buatTransaksi('2026-02-08', $f([
            $item($susuCappuccino, 5),
            $item($susuThai, 3),
            $item($pisangCoklat, 2),
            $item($rotiNanasStraw, 1),
        ]), 'Grup mahasiswa');

        $buatTransaksi('2026-02-12', $f([
            $item($kembangGula, 4),
            $item($kembangJahe, 2),
            $item($sekoteng, 3),
            $item($airMineral, 3),
        ]));

        $buatTransaksi('2026-02-15', $f([
            $item($botol1000ori, 2),
            $item($pudingStraw, 4),
            $item($pudingTaro, 2),
        ]), 'Oleh-oleh');

        $buatTransaksi('2026-02-19', $f([
            $item($susuOreo, 3),
            $item($susuGreenTea, 2),
            $item($pisangCoklatKeju, 3),
            $item($rotiKacangCoklat, 2),
        ]));

        $buatTransaksi('2026-02-22', $f([
            $item($wedangRonde, 2),
            $item($bandrek, 3),
            $item($airJahe, 4),
            $item($kembangSusu, 3),
        ]), 'Malam ramai');

        // ========================
        // MARET 2026
        // ========================
        $buatTransaksi('2026-03-01', $f([
            $item($susuOriginal, 6),
            $item($susuCappuccino, 4),
            $item($botol250ori, 5),
        ]));

        $buatTransaksi('2026-03-05', $f([
            $item($pudingDurian, 3),
            $item($pudingChoco, 4),
            $item($pisangKeju, 3),
            $item($airMineral, 4),
        ]), 'Ramai weekend');

        $buatTransaksi('2026-03-10', $f([
            $item($botol500rasa, 4),
            $item($botol1000ori, 1),
            $item($kembangGula, 5),
        ]));

        $buatTransaksi('2026-03-14', $f([
            $item($susuThai, 6),
            $item($susuChoco, 4),
            $item($rotiNanasStraw, 3),
            $item($rotiKacangCoklat, 2),
        ]), 'Event kampus');

        $buatTransaksi('2026-03-18', $f([
            $item($sekoteng, 5),
            $item($wedangRonde, 3),
            $item($kembangSusu, 4),
            $item($airMineral, 5),
        ]));

        $buatTransaksi('2026-03-22', $f([
            $item($pudingStraw, 5),
            $item($pudingTaro, 3),
            $item($pisangCoklatKeju, 4),
        ]), 'Pesanan keluarga');

        $buatTransaksi('2026-03-28', $f([
            $item($susuOriginal, 8),
            $item($botol250rasa, 6),
            $item($kembangJahe, 4),
            $item($airJahe, 3),
        ]), 'Akhir bulan ramai');

        // ========================
        // APRIL 2026
        // ========================
        $buatTransaksi('2026-04-02', $f([
            $item($botol500ori, 5),
            $item($botol500rasa, 3),
            $item($pudingChoco, 6),
        ]));

        $buatTransaksi('2026-04-07', $f([
            $item($susuCappuccino, 7),
            $item($susuOreo, 5),
            $item($pisangCoklat, 4),
            $item($rotiKacangCoklat, 3),
        ]), 'Hari libur nasional');

        $buatTransaksi('2026-04-12', $f([
            $item($kembangSusu, 6),
            $item($kembangGula, 4),
            $item($sekoteng, 4),
            $item($bandrek, 3),
        ]));

        $buatTransaksi('2026-04-17', $f([
            $item($botol1000ori, 3),
            $item($pudingDurian, 4),
            $item($pudingStraw, 5),
            $item($airMineral, 6),
        ]), 'Pesanan grosir');

        $buatTransaksi('2026-04-21', $f([
            $item($susuGreenTea, 5),
            $item($susuThai, 6),
            $item($rotiNanasStraw, 4),
            $item($pisangCoklatKeju, 3),
        ]));

        $buatTransaksi('2026-04-25', $f([
            $item($susuOriginal, 5),
            $item($kembangJahe, 3),
            $item($wedangRonde, 4),
            $item($airJahe, 5),
            $item($airMineral, 4),
        ]), 'Pelanggan tetap sore');

        $this->command->info('✅ PenjualanSeeder selesai: 20 transaksi berhasil dibuat.');
    }
}
