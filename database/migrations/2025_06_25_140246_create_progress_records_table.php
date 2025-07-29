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
        Schema::create('progress_records', function (Blueprint $table) {
        $table->id();
        $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
        $table->foreignId('teacher_id')->constrained('users')->onDelete('cascade');
        $table->foreignId('learning_module_id')->constrained()->onDelete('cascade');
        $table->enum('assessment', ['lulus', 'mengulang', 'lancar']); // Penilaian dari guru
        $table->text('notes')->nullable(); // Catatan detail dari guru
        $table->date('record_date');
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('progress_records');
    }
};
