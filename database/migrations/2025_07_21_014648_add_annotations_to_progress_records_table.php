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
        // Kita gunakan tipe JSON agar lebih fleksibel
        $table->json('annotations')->nullable()->after('notes');
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('progress_records', function (Blueprint $table) {
        $table->dropColumn('annotations');
    });
    }
};
