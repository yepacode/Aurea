<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Columnas opcionales para la traducción al inglés del catálogo.
 *
 * Los productos son de proveedor colombiano, así que la fuente de verdad
 * queda en español. Cuando estas columnas están llenas y el locale activo
 * es "en", el helper Product::localizedName()/localizedDescription() las
 * devuelve; si no, cae al valor español original.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table): void {
            $table->string('name_en')->nullable()->after('name');
            $table->text('description_en')->nullable()->after('description');
            $table->string('meta_title_en')->nullable()->after('meta_title');
            $table->string('meta_description_en', 500)->nullable()->after('meta_description');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table): void {
            $table->dropColumn([
                'name_en',
                'description_en',
                'meta_title_en',
                'meta_description_en',
            ]);
        });
    }
};
