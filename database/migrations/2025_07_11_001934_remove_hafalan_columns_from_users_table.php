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
        Schema::table('users', function (Blueprint $table) {
        // Cek dulu apakah kolomnya ada sebelum dihapus, untuk menghindari error
        if (Schema::hasColumn('users', 'completed_juz')) {
            $table->dropColumn('completed_juz');
        }
        if (Schema::hasColumn('users', 'completed_pages')) {
            $table->dropColumn('completed_pages');
        }
        if (Schema::hasColumn('users', 'completed_ayahs')) {
            $table->dropColumn('completed_ayahs');
        }
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
