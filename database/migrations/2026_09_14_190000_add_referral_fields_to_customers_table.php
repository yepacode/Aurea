<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Programa "Recomienda y gana":
 *   - referral_code:              código único que cada cliente comparte por WhatsApp/redes.
 *   - referred_by_customer_id:    quién la refirió (null si vino sola).
 *   - referral_source:            de dónde llegó (whatsapp / instagram / direct / etc.).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            if (! Schema::hasColumn('customers', 'referral_code')) {
                $table->string('referral_code', 12)->nullable()->unique()->after('remember_token');
            }
            if (! Schema::hasColumn('customers', 'referred_by_customer_id')) {
                $table->foreignId('referred_by_customer_id')
                    ->nullable()
                    ->after('referral_code')
                    ->constrained('customers')
                    ->nullOnDelete();
            }
            if (! Schema::hasColumn('customers', 'referral_source')) {
                $table->string('referral_source', 40)->nullable()->after('referred_by_customer_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            if (Schema::hasColumn('customers', 'referred_by_customer_id')) {
                // Drop FK antes que la columna en MySQL.
                try {
                    $table->dropForeign(['referred_by_customer_id']);
                } catch (\Throwable $e) {
                    // fallback si el nombre difiere
                }
                $table->dropColumn('referred_by_customer_id');
            }
            if (Schema::hasColumn('customers', 'referral_source')) {
                $table->dropColumn('referral_source');
            }
            if (Schema::hasColumn('customers', 'referral_code')) {
                $table->dropUnique(['referral_code']);
                $table->dropColumn('referral_code');
            }
        });
    }
};
