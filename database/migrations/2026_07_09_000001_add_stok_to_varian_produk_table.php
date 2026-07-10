<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('varian_produk', function (Blueprint $table) {
            $table->decimal('stok', 10, 2)->default(0)->after('harga');
        });
    }

    public function down(): void
    {
        Schema::table('varian_produk', function (Blueprint $table) {
            $table->dropColumn('stok');
        });
    }
};
