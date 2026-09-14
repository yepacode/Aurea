<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Movimientos del programa de puntos de fidelidad.
 * Cada fila representa un evento sobre el balance de un cliente:
 *   - earned:   ganó puntos por una compra
 *   - redeemed: canjeó puntos (negativos)
 *   - expired:  vencieron sin usar (negativos)
 *   - adjusted: ajuste manual desde admin (positivos o negativos)
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loyalty_points', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')
                ->constrained('customers')
                ->cascadeOnDelete();
            $table->integer('points'); // positivo = ganado; negativo = canjeado/vencido
            $table->enum('type', ['earned', 'redeemed', 'expired', 'adjusted'])->default('earned');
            $table->foreignId('order_id')
                ->nullable()
                ->constrained('orders')
                ->nullOnDelete();
            $table->string('note', 255)->nullable();
            $table->dateTime('expires_at')->nullable();
            $table->timestamps();

            $table->index('customer_id');
            $table->index('order_id');
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loyalty_points');
    }
};
