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
        if (Schema::hasTable('bills') && !Schema::hasColumn('bills', 'invoice_number')) {
            Schema::table('bills', function (Blueprint $table) {
                $table->string('invoice_number', 64)->nullable()->unique()->after('id');
                $table->string('va_number', 32)->nullable()->index()->after('invoice_number');
                $table->string('description', 255)->nullable()->after('due_date');
                $table->unique(['payment_period_id', 'mahasiswa_id'], 'bill_period_mahasiswa_unique');
            });
        }

        if (Schema::hasTable('payments') && !Schema::hasColumn('payments', 'security_hash')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->string('security_hash', 128)->nullable()->after('reference_number');
                $table->string('status', 32)->default('success')->after('security_hash');
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete()->after('status');
                $table->text('notes')->nullable()->after('user_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('payments') && Schema::hasColumn('payments', 'security_hash')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
                $table->dropColumn(['security_hash', 'status', 'user_id', 'notes']);
            });
        }

        if (Schema::hasTable('bills') && Schema::hasColumn('bills', 'invoice_number')) {
            Schema::table('bills', function (Blueprint $table) {
                $table->dropUnique('bill_period_mahasiswa_unique');
                $table->dropColumn(['invoice_number', 'va_number', 'description']);
            });
        }
    }
};
