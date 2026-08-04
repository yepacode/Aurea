<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // Valores por defecto: se toman del .env si existen, para no perder la
        // configuración actual al migrar.
        $defaults = [
            'stripe_key' => env('STRIPE_KEY', ''),
            'stripe_secret' => env('STRIPE_SECRET', ''),
            'stripe_webhook_secret' => env('STRIPE_WEBHOOK_SECRET', ''),
        ];

        foreach ($defaults as $key => $value) {
            DB::table('payment_settings')->insert([
                'key' => $key,
                'value' => $value,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_settings');
    }
};
