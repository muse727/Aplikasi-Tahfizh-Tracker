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
        Schema::create('invoices', function (Blueprint $table) {
        $table->id();
        $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
        $table->string('title'); // e.g., "SPP Bulan Juli 2025"
        $table->integer('month');
        $table->integer('year');
        $table->decimal('amount', 15, 2); // Jumlah total tagihan
        $table->decimal('amount_paid', 15, 2)->default(0); // Jumlah yang sudah dibayar
        $table->date('due_date'); // Tanggal jatuh tempo
        $table->enum('status', ['paid', 'partial', 'unpaid', 'overdue'])->default('unpaid');
        $table->timestamp('paid_at')->nullable(); // Waktu lunas
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
