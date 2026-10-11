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
        // 1. payment_periods
        if (!Schema::hasTable('payment_periods')) {
            Schema::create('payment_periods', function (Blueprint $table) {
                $table->id();
                $table->string('name'); // Contoh: Semester Ganjil 2025/2026
                $table->date('start_date');
                $table->date('end_date');
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // 2. bills
        if (!Schema::hasTable('bills')) {
            Schema::create('bills', function (Blueprint $table) {
                $table->id();
                $table->string('invoice_number', 64)->nullable()->unique();
                $table->string('va_number', 32)->nullable()->index();
                $table->foreignId('payment_period_id')->constrained('payment_periods')->onDelete('cascade');
                $table->foreignId('mahasiswa_id')->constrained('mahasiswa')->onDelete('cascade');
                $table->decimal('amount', 15, 2);
                $table->string('status')->default('unpaid'); // unpaid, paid
                $table->date('due_date')->nullable();
                $table->string('description', 255)->nullable();
                $table->timestamps();
                $table->unique(['payment_period_id', 'mahasiswa_id'], 'bill_period_mahasiswa_unique');
            });
        }

        // 3. payments
        if (!Schema::hasTable('payments')) {
            Schema::create('payments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('bill_id')->constrained('bills')->onDelete('cascade');
                $table->decimal('amount_paid', 15, 2);
                $table->dateTime('payment_date');
                $table->string('payment_method')->nullable();
                $table->string('reference_number', 128)->unique();
                $table->string('idempotency_key', 128)->nullable()->unique();
                $table->string('security_hash', 128)->nullable();
                $table->string('status', 32)->default('success');
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
        Schema::dropIfExists('bills');
        Schema::dropIfExists('payment_periods');
    }
};
