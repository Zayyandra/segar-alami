<?php

namespace Database\Seeders;

use App\Models\BahanBaku;
use App\Models\Kategori;
use App\Models\Produk;
use App\Models\VarianProduk;
use Illuminate\Database\Seeder;

class SegarAlamiSeeder extends Seeder
{
    public function run(): void
    {
        // =====================
        // KATEGORI
        // =====================
        $kategoriData = [
            ['nama' => 'Susu Kedelai',   'urutan' => 1, 'is_active' => true],
            ['nama' => 'Kembang Tahu',   'urutan' => 2, 'is_active' => true],
            ['nama' => 'Puding Kedelai', 'urutan' => 3, 'is_active' => true],
            ['nama' => 'Snack',          'urutan' => 4, 'is_active' => true],
            ['nama' => 'Minuman',        'urutan' => 5, 'is_active' => true],
        ];

        foreach ($kategoriData as $k) {
            Kategori::firstOrCreate(['nama' => $k['nama']], $k);
        }

        $susuKedelai   = Kategori::where('nama', 'Susu Kedelai')->first();
        $kembangTahu   = Kategori::where('nama', 'Kembang Tahu')->first();
        $pudingKedelai = Kategori::where('nama', 'Puding Kedelai')->first();
        $snack         = Kategori::where('nama', 'Snack')->first();
        $minuman       = Kategori::where('nama', 'Minuman')->first();

        // =====================
        // PRODUK + VARIAN
        // =====================

        // 1. Susu Kedelai Gelas
        $susuGelas = Produk::firstOrCreate(
            ['nama' => 'Susu Kedelai Gelas'],
            ['kategori_id' => $susuKedelai->id, 'deskripsi' => 'Susu kedelai segar dalam gelas plastik.', 'is_active' => true]
        );
        $susuGelasVarians = [
            ['nama_varian' => 'Original',          'ukuran' => null, 'harga' => 8000],
            ['nama_varian' => 'Original + Air Jahe','ukuran' => null, 'harga' => 9000],
            ['nama_varian' => 'Cappuccino',         'ukuran' => null, 'harga' => 12000],
            ['nama_varian' => 'Moccachino',         'ukuran' => null, 'harga' => 12000],
            ['nama_varian' => 'Coffee Caramel',     'ukuran' => null, 'harga' => 12000],
            ['nama_varian' => 'Chocolate Caramel',  'ukuran' => null, 'harga' => 12000],
            ['nama_varian' => 'Creamy Chocolate',   'ukuran' => null, 'harga' => 12000],
            ['nama_varian' => 'Black Forest',        'ukuran' => null, 'harga' => 12000],
            ['nama_varian' => 'Oreo',               'ukuran' => null, 'harga' => 12000],
            ['nama_varian' => 'Green Tea',           'ukuran' => null, 'harga' => 12000],
            ['nama_varian' => 'Milk Tea',            'ukuran' => null, 'harga' => 12000],
            ['nama_varian' => 'Thai Tea',            'ukuran' => null, 'harga' => 12000],
            ['nama_varian' => 'Vanilla',             'ukuran' => null, 'harga' => 12000],
            ['nama_varian' => 'Vanilla Latte',       'ukuran' => null, 'harga' => 12000],
            ['nama_varian' => 'Tiramisu',            'ukuran' => null, 'harga' => 12000],
            ['nama_varian' => 'Hazelnut',            'ukuran' => null, 'harga' => 12000],
            ['nama_varian' => 'Avocado',             'ukuran' => null, 'harga' => 12000],
            ['nama_varian' => 'Melon',               'ukuran' => null, 'harga' => 12000],
            ['nama_varian' => 'Taro',                'ukuran' => null, 'harga' => 12000],
            ['nama_varian' => 'Ovomaltine',          'ukuran' => null, 'harga' => 12000],
            ['nama_varian' => 'Nutella',             'ukuran' => null, 'harga' => 12000],
            ['nama_varian' => 'Red Velvet',          'ukuran' => null, 'harga' => 12000],
            ['nama_varian' => 'Bubble Gum',          'ukuran' => null, 'harga' => 12000],
        ];
        foreach ($susuGelasVarians as $v) {
            VarianProduk::firstOrCreate(
                ['produk_id' => $susuGelas->id, 'nama_varian' => $v['nama_varian']],
                ['ukuran' => $v['ukuran'], 'harga' => $v['harga'], 'is_active' => true]
            );
        }

        // 2. Susu Kedelai Botol
        $susuBotol = Produk::firstOrCreate(
            ['nama' => 'Susu Kedelai Botol'],
            ['kategori_id' => $susuKedelai->id, 'deskripsi' => 'Susu kedelai dalam kemasan botol, tersedia 3 ukuran.', 'is_active' => true]
        );
        $susuBotolVarians = [
            ['nama_varian' => 'Original',      'ukuran' => '250ml',  'harga' => 11000],
            ['nama_varian' => 'Varian Rasa',   'ukuran' => '250ml',  'harga' => 14000],
            ['nama_varian' => 'Original',      'ukuran' => '500ml',  'harga' => 16000],
            ['nama_varian' => 'Varian Rasa',   'ukuran' => '500ml',  'harga' => 25000],
            ['nama_varian' => 'Original',      'ukuran' => '1000ml', 'harga' => 27000],
            ['nama_varian' => 'Varian Rasa',   'ukuran' => '1000ml', 'harga' => 45000],
        ];
        foreach ($susuBotolVarians as $v) {
            VarianProduk::firstOrCreate(
                ['produk_id' => $susuBotol->id, 'nama_varian' => $v['nama_varian'], 'ukuran' => $v['ukuran']],
                ['harga' => $v['harga'], 'is_active' => true]
            );
        }

        // 3. Kembang Tahu
        $kembang = Produk::firstOrCreate(
            ['nama' => 'Kembang Tahu'],
            ['kategori_id' => $kembangTahu->id, 'deskripsi' => 'Kembang tahu segar khas Segar Alami.', 'is_active' => true]
        );
        $kembangVarians = [
            ['nama_varian' => 'Susu Kedelai',              'ukuran' => null, 'harga' => 10000],
            ['nama_varian' => 'Gula Putih/Merah',          'ukuran' => null, 'harga' => 9000],
            ['nama_varian' => 'Gula Merah + Jahe',         'ukuran' => null, 'harga' => 9000],
        ];
        foreach ($kembangVarians as $v) {
            VarianProduk::firstOrCreate(
                ['produk_id' => $kembang->id, 'nama_varian' => $v['nama_varian']],
                ['ukuran' => $v['ukuran'], 'harga' => $v['harga'], 'is_active' => true]
            );
        }

        // 4. Puding Kedelai
        $puding = Produk::firstOrCreate(
            ['nama' => 'Puding Kedelai'],
            ['kategori_id' => $pudingKedelai->id, 'deskripsi' => 'Puding kedelai lembut dengan berbagai rasa.', 'is_active' => true]
        );
        $pudingVarians = [
            ['nama_varian' => 'Original',    'harga' => 11000],
            ['nama_varian' => 'Durian',      'harga' => 14000],
            ['nama_varian' => 'Grape',       'harga' => 14000],
            ['nama_varian' => 'Lychee',      'harga' => 14000],
            ['nama_varian' => 'Mango',       'harga' => 14000],
            ['nama_varian' => 'Strawberry',  'harga' => 14000],
            ['nama_varian' => 'Avocado',     'harga' => 14000],
            ['nama_varian' => 'Melon',       'harga' => 14000],
            ['nama_varian' => 'Taro',        'harga' => 14000],
            ['nama_varian' => 'Green Tea',   'harga' => 14000],
            ['nama_varian' => 'Vanilla',     'harga' => 14000],
            ['nama_varian' => 'Tiramisu',    'harga' => 14000],
            ['nama_varian' => 'Hazelnut',    'harga' => 14000],
            ['nama_varian' => 'Chocolate',   'harga' => 14000],
            ['nama_varian' => 'Black Forest','harga' => 14000],
            ['nama_varian' => 'Oreo',        'harga' => 14000],
            ['nama_varian' => 'Red Velvet',  'harga' => 14000],
            ['nama_varian' => 'Bubble Gum',  'harga' => 14000],
        ];
        foreach ($pudingVarians as $v) {
            VarianProduk::firstOrCreate(
                ['produk_id' => $puding->id, 'nama_varian' => $v['nama_varian']],
                ['ukuran' => null, 'harga' => $v['harga'], 'is_active' => true]
            );
        }

        // 5. Pisang Bakar
        $pisang = Produk::firstOrCreate(
            ['nama' => 'Pisang Bakar'],
            ['kategori_id' => $snack->id, 'deskripsi' => 'Pisang bakar dengan berbagai topping.', 'is_active' => true]
        );
        $pisangVarians = [
            ['nama_varian' => 'Coklat',     'harga' => 12000],
            ['nama_varian' => 'Keju',       'harga' => 13000],
            ['nama_varian' => 'Coklat Keju','harga' => 14000],
        ];
        foreach ($pisangVarians as $v) {
            VarianProduk::firstOrCreate(
                ['produk_id' => $pisang->id, 'nama_varian' => $v['nama_varian']],
                ['ukuran' => null, 'harga' => $v['harga'], 'is_active' => true]
            );
        }

        // 6. Roti Bakar
        $roti = Produk::firstOrCreate(
            ['nama' => 'Roti Bakar'],
            ['kategori_id' => $snack->id, 'deskripsi' => 'Roti bakar 2 rasa, bisa request 4 rasa (+Rp 2.000).', 'is_active' => true]
        );
        $rotiVarians = [
            ['nama_varian' => 'Nanas-Stroberi',                    'harga' => 19000],
            ['nama_varian' => 'Kacang-Coklat',                     'harga' => 20000],
            ['nama_varian' => 'Nanas/Stroberi-Kacang/Coklat/Anggur','harga' => 20000],
            ['nama_varian' => 'Nanas/Stroberi-Srikaya/Vanila/Keju', 'harga' => 21000],
            ['nama_varian' => 'Kacang/Coklat-Srikaya/Anggur',      'harga' => 22000],
            ['nama_varian' => 'Kacang/Coklat-Vanila/Keju',         'harga' => 22000],
            ['nama_varian' => 'Anggur/Keju-Srikaya/Vanilla',       'harga' => 22000],
        ];
        foreach ($rotiVarians as $v) {
            VarianProduk::firstOrCreate(
                ['produk_id' => $roti->id, 'nama_varian' => $v['nama_varian']],
                ['ukuran' => null, 'harga' => $v['harga'], 'is_active' => true]
            );
        }

        // 7. Minuman
        $minumanProd = Produk::firstOrCreate(
            ['nama' => 'Minuman Hangat'],
            ['kategori_id' => $minuman->id, 'deskripsi' => 'Minuman hangat tradisional.', 'is_active' => true]
        );
        $minumanVarians = [
            ['nama_varian' => 'Air Jahe',       'harga' => 9000],
            ['nama_varian' => 'Bandrek',         'harga' => 12000],
            ['nama_varian' => 'Sekoteng',        'harga' => 13000],
            ['nama_varian' => 'Bandrek Telor',   'harga' => 15000],
            ['nama_varian' => 'Sekoteng Telor',  'harga' => 15000],
            ['nama_varian' => 'Wedang Ronde',    'harga' => 16000],
            ['nama_varian' => 'Air Mineral',     'harga' => 2000],
        ];
        foreach ($minumanVarians as $v) {
            VarianProduk::firstOrCreate(
                ['produk_id' => $minumanProd->id, 'nama_varian' => $v['nama_varian']],
                ['ukuran' => null, 'harga' => $v['harga'], 'is_active' => true]
            );
        }

        // =====================
        // BAHAN BAKU
        // =====================
        $bahanBakuData = [
            ['nama' => 'Kacang Kedelai',  'satuan' => 'kg',  'kategori_bb' => 'utama',     'stok_saat_ini' => 50,  'stok_minimum' => 10, 'harga_per_satuan' => 15000],
            ['nama' => 'Gula Pasir',      'satuan' => 'kg',  'kategori_bb' => 'utama',     'stok_saat_ini' => 30,  'stok_minimum' => 5,  'harga_per_satuan' => 14000],
            ['nama' => 'Daun Pandan',     'satuan' => 'ikat','kategori_bb' => 'pendukung',     'stok_saat_ini' => 10,  'stok_minimum' => 3,  'harga_per_satuan' => 3000],
            ['nama' => 'Tepung Biang',    'satuan' => 'kg',  'kategori_bb' => 'utama',     'stok_saat_ini' => 5,   'stok_minimum' => 2,  'harga_per_satuan' => 20000],
            ['nama' => 'Soka',            'satuan' => 'kg',  'kategori_bb' => 'utama',     'stok_saat_ini' => 3,   'stok_minimum' => 1,  'harga_per_satuan' => 25000],
            ['nama' => 'Pisang',          'satuan' => 'sisir','kategori_bb' => 'pendukung', 'stok_saat_ini' => 8,   'stok_minimum' => 2,  'harga_per_satuan' => 15000],
            ['nama' => 'Roti Tawar',      'satuan' => 'bungkus','kategori_bb' => 'pendukung','stok_saat_ini' => 20, 'stok_minimum' => 5,  'harga_per_satuan' => 12000],
            ['nama' => 'Gula Merah',      'satuan' => 'kg',  'kategori_bb' => 'pendukung', 'stok_saat_ini' => 10,  'stok_minimum' => 2,  'harga_per_satuan' => 18000],
            ['nama' => 'Jahe',            'satuan' => 'kg',  'kategori_bb' => 'pendukung', 'stok_saat_ini' => 5,   'stok_minimum' => 1,  'harga_per_satuan' => 20000],
            ['nama' => 'Gelas Plastik',   'satuan' => 'pack','kategori_bb' => 'pendukung', 'stok_saat_ini' => 15,  'stok_minimum' => 3,  'harga_per_satuan' => 25000],
            ['nama' => 'Mangkok Plastik', 'satuan' => 'pack','kategori_bb' => 'pendukung', 'stok_saat_ini' => 10,  'stok_minimum' => 2,  'harga_per_satuan' => 20000],
            ['nama' => 'Botol 250ml',     'satuan' => 'pack','kategori_bb' => 'pendukung', 'stok_saat_ini' => 10,  'stok_minimum' => 2,  'harga_per_satuan' => 30000],
            ['nama' => 'Botol 500ml',     'satuan' => 'pack','kategori_bb' => 'pendukung', 'stok_saat_ini' => 8,   'stok_minimum' => 2,  'harga_per_satuan' => 35000],
            ['nama' => 'Botol 1000ml',    'satuan' => 'pack','kategori_bb' => 'pendukung', 'stok_saat_ini' => 5,   'stok_minimum' => 1,  'harga_per_satuan' => 45000],
        ];

        foreach ($bahanBakuData as $b) {
            BahanBaku::firstOrCreate(
                ['nama' => $b['nama']],
                array_merge($b, ['tracking_ss_rop' => false, 'is_active' => true])
            );
        }

        $this->command->info('✅ Data Segar Alami berhasil di-seed!');
        $this->command->info('   - ' . Kategori::count() . ' kategori');
        $this->command->info('   - ' . Produk::count() . ' produk');
        $this->command->info('   - ' . VarianProduk::count() . ' varian produk');
        $this->command->info('   - ' . BahanBaku::count() . ' bahan baku');
    }
}
