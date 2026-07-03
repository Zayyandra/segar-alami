<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateHargaSeeder extends Seeder
{
    public function run(): void
    {
        $updates = [
                         // Susu Kedelai Gelas
            1  => 9000,  // Original: 8000 → 9000
            2  => 10000, // Original + Air Jahe: 9000 → 10000

                         // Kembang Tahu
            30 => 11000, // Susu Kedelai: 10000 → 11000
            31 => 10000, // Kembang Tahu Gula Putih/Merah: 9000 → 10000
            32 => 10000, // Gula Merah + Jahe: 9000 → 10000

            33 => 12000, // Puding Original: 11000 → 12000
                         // Puding Kedelai — varian rasa semua 14000 → 15000
            34 => 15000, // Durian
            35 => 15000, // Grape
            36 => 15000, // Lychee
            37 => 15000, // Mango
            38 => 15000, // Strawberry
            39 => 15000, // Avocado
            40 => 15000, // Melon
            41 => 15000, // Taro
            42 => 15000, // Green Tea
            43 => 15000, // Vanilla
            44 => 15000, // Tiramisu
            45 => 15000, // Hazelnut
            46 => 15000, // Chocolate
            47 => 15000, // Black Forest
            48 => 15000, // Oreo
            49 => 15000, // Red Velvet
            50 => 15000, // Bubble Gum

                         // Susu Kedelai Botol
            27 => 21000, // 500ml Varian Rasa: 25000 → 21000
            29 => 35000, // 1000ml Varian Rasa: 45000 → 35000

                        // Minuman Hangat
            67 => 1000, // Air Mineral: 2000 → 1000
        ];

        foreach ($updates as $id => $harga) {
            DB::table('varian_produk')
                ->where('id', $id)
                ->update([
                    'harga'      => $harga,
                    'updated_at' => now(),
                ]);
        }

        $this->command->info('Harga varian produk berhasil diperbarui sesuai daftar menu.');
    }
}
