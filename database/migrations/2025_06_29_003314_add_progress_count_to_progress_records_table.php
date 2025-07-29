<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('progress_records', function (Blueprint $table) {
        // Kolom untuk menyimpan jumlah (misal: jumlah ayat, jumlah halaman)
        $table->integer('progress_count')->default(0)->after('notes');
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('progress_records', function (Blueprint $table) {
            //
        });
    }
};
