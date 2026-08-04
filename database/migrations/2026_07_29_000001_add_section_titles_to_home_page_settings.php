<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('home_page_settings', function (Blueprint $table) {
            $table->string('authority_title')->nullable()->after('comparison_with_items');
            $table->string('testimonials_title')->nullable()->after('authority_title');
            $table->string('faq_title')->nullable()->after('testimonials_title');
        });

        // Sembrar con los textos actuales para que el admin los muestre poblados.
        DB::table('home_page_settings')->update([
            'authority_title' => 'Tu distribuidora de productos profesionales de belleza',
            'testimonials_title' => 'Lo que dicen nuestras clientes',
            'faq_title' => 'Preguntas frecuentes',
        ]);
    }

    public function down(): void
    {
        Schema::table('home_page_settings', function (Blueprint $table) {
            $table->dropColumn(['authority_title', 'testimonials_title', 'faq_title']);
        });
    }
};
