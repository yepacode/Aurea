<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Asigna la marca "Miracle" a TODO producto sin marca (brand_id NULL).
 *
 * Belleza Áurea es distribuidora multimarca: ninguna ficha debe mostrarse bajo
 * el nombre de la tienda. El grueso del catálogo proviene de Miracle, así que
 * Miracle es la marca por defecto. Los productos que YA tienen marca no se
 * tocan (los 5 de Jan Beauty y los que ya son Miracle se preservan). Las
 * excepciones de otras marcas (Bella Italia, etc.) se corrigen aparte.
 *
 * Idempotente: si no hay productos sin marca, es un no-op. Si no existe la
 * marca Miracle, no hace nada (evita dejar productos mal marcados).
 */
return new class extends Migration
{
    public function up(): void
    {
        $miracle = DB::table('brands')
            ->where(function ($q) {
                $q->where('slug', 'miracle')->orWhere('name', 'Miracle');
            })
            ->orderBy('id')
            ->first();

        if (! $miracle) {
            return;
        }

        DB::table('products')
            ->whereNull('brand_id')
            ->update([
                'brand_id'   => $miracle->id,
                'updated_at' => now(),
            ]);
    }

    public function down(): void
    {
        // Migración de datos: no se revierte automáticamente, porque no se
        // guarda qué productos estaban sin marca antes de ejecutarla.
    }
};
