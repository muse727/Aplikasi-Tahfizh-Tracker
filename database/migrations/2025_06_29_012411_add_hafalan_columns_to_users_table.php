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
        // Hapus kolom lama jika ada
        if (Schema::hasColumn('users', 'total_ayahs_memorized')) {
            $table->dropColumn('total_ayahs_memorized');
        }

        // Tambahkan 3 kolom baru
        $table->integer('completed_juz')->default(0)->after('role');
        $table->integer('completed_pages')->default(0)->after('completed_juz');
        $table->integer('completed_ayahs')->default(0)->after('completed_pages');
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
