<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bahan_baku', function (Blueprint $table) {
            $table->decimal('stok_saat_ini', 10, 2)->default(0)->after('satuan');
            $table->decimal('stok_minimum', 10, 2)->default(0)->after('stok_saat_ini');
            $table->decimal('harga_per_satuan', 10, 2)->default(0)->after('stok_minimum');
        });
    }

    public function down(): void
    {
        Schema::table('bahan_baku', function (Blueprint $table) {
            $table->dropColumn(['stok_saat_ini', 'stok_minimum', 'harga_per_satuan']);
        });
    }
};
