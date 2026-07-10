<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bahan_keluar_batch', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bahan_keluar_id')->constrained('bahan_keluar')->cascadeOnDelete();
            $table->foreignId('bahan_masuk_id')->constrained('bahan_masuk')->cascadeOnDelete();
            $table->decimal('jumlah_diambil', 10, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bahan_keluar_batch');
    }
};
