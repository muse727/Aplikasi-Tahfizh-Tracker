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
        // Menambahkan kolom 'details' dengan tipe JSON setelah kolom 'notes'
        $table->json('details')->nullable()->after('notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('progress_records', function (Blueprint $table) {
        // Perintah untuk menghapus kolom jika migrasi di-rollback
        $table->dropColumn('details');
        });
    }
};
