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
        Schema::create('learning_modules', function (Blueprint $table) {
        $table->id();
        // Ini menghubungkan setiap materi ke kelasnya.
        $table->foreignId('course_id')->constrained()->onDelete('cascade');
        $table->string('module_name'); // Contoh: "Halaman 5", "Surah An-Naba Ayat 1-20"
        $table->string('type'); // Contoh: 'halaman_tilawati', 'bacaan_quran', 'hafalan'
        $table->integer('order_column')->default(0); // Untuk mengurutkan materi
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('learning_modules');
    }
};
