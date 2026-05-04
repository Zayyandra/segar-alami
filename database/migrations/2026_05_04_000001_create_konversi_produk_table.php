<?php
// database/migrations/2026_05_04_000001_create_konversi_produk_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('konversi_produk', function (Blueprint $table) {
            $table->id();
            $table->foreignId('varian_produk_id')->constrained('varian_produk')->cascadeOnDelete();
            $table->foreignId('bahan_baku_id')->constrained('bahan_baku')->cascadeOnDelete();
            $table->decimal('jumlah_per_satuan', 8, 4);
            $table->timestamps();
            $table->unique(['varian_produk_id', 'bahan_baku_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('konversi_produk');
    }
};
