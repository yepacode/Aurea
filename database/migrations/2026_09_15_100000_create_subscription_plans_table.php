<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->decimal('base_price', 10, 2)->default(0);         // precio con descuento (lo que paga)
            $table->decimal('regular_price', 10, 2)->default(0);       // suma de precios sueltos (para tachar)
            $table->unsignedTinyInteger('discount_percent')->default(0);
            $table->unsignedInteger('interval_days')->default(30);
            $table->string('delivery_days_message')->nullable();       // "Llega en 3-5 días"
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_plans');
    }
};
