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
        // Cek dulu agar tidak error jika kolom sudah ada
        if (!Schema::hasColumn('users', 'completed_juz')) {
            $table->integer('completed_juz')->default(0)->after('role');
        }
        if (!Schema::hasColumn('users', 'completed_pages')) {
            $table->integer('completed_pages')->default(0)->after('completed_juz');
        }
        if (!Schema::hasColumn('users', 'completed_ayahs')) {
            $table->integer('completed_ayahs')->default(0)->after('completed_pages');
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
