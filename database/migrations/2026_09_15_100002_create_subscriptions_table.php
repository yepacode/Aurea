<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subscription_plan_id')->constrained()->cascadeOnDelete();
            $table->enum('status', ['active', 'paused', 'cancelled'])->default('active');
            $table->dateTime('next_delivery_at');
            $table->dateTime('last_delivery_at')->nullable();
            $table->unsignedInteger('total_deliveries')->default(0);
            $table->string('payment_method')->default('epayco'); // epayco | cash_on_delivery
            $table->json('shipping_address_json')->nullable();
            $table->dateTime('cancelled_at')->nullable();
            $table->string('pause_reason')->nullable();
            $table->timestamps();

            $table->index(['status', 'next_delivery_at']);
            $table->index('customer_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
