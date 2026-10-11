<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('payments') && !Schema::hasColumn('payments', 'idempotency_key')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->string('idempotency_key', 128)->nullable()->after('reference_number');
            });

            // Sync existing reference_number values into idempotency_key
            DB::table('payments')->whereNull('idempotency_key')->update([
                'idempotency_key' => DB::raw('reference_number')
            ]);

            Schema::table('payments', function (Blueprint $table) {
                $table->unique('idempotency_key', 'payments_idempotency_key_unique');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('payments') && Schema::hasColumn('payments', 'idempotency_key')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->dropUnique('payments_idempotency_key_unique');
                $table->dropColumn('idempotency_key');
            });
        }
    }
};
