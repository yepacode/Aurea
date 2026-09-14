<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Registro del programa "Recomienda y gana".
 * Se crea una fila cuando una nueva cliente se registra con un código de referida
 * en sesión; queda "pending" hasta que la referida complete su PRIMERA compra pagada,
 * momento en el que pasa a "completed" y se otorgan los puntos a ambas.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('referrals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('referrer_customer_id')
                ->constrained('customers')
                ->cascadeOnDelete();
            $table->foreignId('referred_customer_id')
                ->constrained('customers')
                ->cascadeOnDelete();
            $table->foreignId('first_order_id')
                ->nullable()
                ->constrained('orders')
                ->nullOnDelete();
            $table->enum('status', ['pending', 'completed'])->default('pending');
            $table->unsignedInteger('reward_referrer_points')->default(500);
            $table->unsignedInteger('reward_referred_points')->default(500);
            $table->timestamps();

            // Una amiga solo puede haber sido referida por una persona.
            $table->unique('referred_customer_id');
            $table->index('referrer_customer_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('referrals');
    }
};
