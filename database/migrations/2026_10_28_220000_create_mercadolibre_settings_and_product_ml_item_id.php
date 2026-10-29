<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabla key-value para los tokens de la API de Mercado Libre
        // (access_token, refresh_token, expires_at, ml_user_id). Mismo patrón
        // que analytics_settings.
        Schema::create('mercadolibre_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // Guardamos el id de la publicación de ML en el producto para saber
        // cuáles ya están publicados y no duplicarlos.
        Schema::table('products', function (Blueprint $table) {
            if (! Schema::hasColumn('products', 'ml_item_id')) {
                $table->string('ml_item_id')->nullable()->index();
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mercadolibre_settings');

        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'ml_item_id')) {
                $table->dropColumn('ml_item_id');
            }
        });
    }
};
