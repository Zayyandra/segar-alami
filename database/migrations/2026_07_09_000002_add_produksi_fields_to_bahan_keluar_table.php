<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bahan_keluar', function (Blueprint $table) {
            $table->foreignId('varian_produk_id')
                ->nullable()
                ->after('bahan_baku_id')
                ->constrained('varian_produk')
                ->nullOnDelete();

            $table->decimal('hasil_produksi', 10, 2)
                ->nullable()
                ->after('jumlah')
                ->comment('Jumlah unit varian produk yang dihasilkan otomatis dari konversi_produk');
        });
    }

    public function down(): void
    {
        Schema::table('bahan_keluar', function (Blueprint $table) {
            $table->dropConstrainedForeignId('varian_produk_id');
            $table->dropColumn('hasil_produksi');
        });
    }
};
