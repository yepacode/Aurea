<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Subscripciones a Web Push (VAPID). Un mismo endpoint = una suscripción
 * única. Puede pertenecer a un cliente logueado o a un invitado (session_id).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('push_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->string('session_id')->nullable();
            $table->string('endpoint', 500)->unique();
            $table->string('public_key', 255);
            $table->string('auth_token', 255);
            $table->string('user_agent')->nullable();
            $table->timestamps();

            $table->index('customer_id');
            $table->index('session_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('push_subscriptions');
    }
};
