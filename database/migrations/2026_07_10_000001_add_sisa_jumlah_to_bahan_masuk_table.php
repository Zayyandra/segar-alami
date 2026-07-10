<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bahan_masuk', function (Blueprint $table) {
            $table->decimal('sisa_jumlah', 10, 2)->nullable()->after('jumlah');
        });

        DB::table('bahan_masuk')->update(['sisa_jumlah' => DB::raw('jumlah')]);
    }

    public function down(): void
    {
        Schema::table('bahan_masuk', function (Blueprint $table) {
            $table->dropColumn('sisa_jumlah');
        });
    }
};
