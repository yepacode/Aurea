<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Carritos abandonados de clientes con cuenta (para recordatorio por correo).
 * Se persiste una fila por cliente; se borra al vaciar el carrito o al comprar.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('abandoned_carts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->unique()->constrained()->cascadeOnDelete();
            $table->json('items');            // snapshot: [{name, qty, unit_price, image, slug}]
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->timestamp('reminded_at')->nullable();
            $table->timestamps();
            $table->index(['reminded_at', 'updated_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('abandoned_carts');
    }
};
