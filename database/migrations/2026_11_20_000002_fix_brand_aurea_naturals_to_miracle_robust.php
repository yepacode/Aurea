<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Corrige la marca "Áurea Naturals" (placeholder) -> "Miracle", de forma robusta
 * e idempotente. Reemplaza a la 000001: el match va por LIKE '%urea Naturals%'
 * para no depender del acento/encoding exacto. Si ya está corregida, no hace nada.
 *
 * Belleza Áurea DISTRIBUYE Miracle; no es el fabricante.
 */
return new class extends Migration
{
    public function up(): void
    {
        $aurea = DB::table('brands')
            ->where('name', 'like', '%urea Naturals%')
            ->first();

        if (! $aurea) {
            return; // ya corregida o no existe
        }

        $existingMiracle = DB::table('brands')
            ->where('name', 'Miracle')
            ->where('id', '!=', $aurea->id)
            ->first();

        if ($existingMiracle) {
            DB::table('products')->where('brand_id', $aurea->id)
                ->update(['brand_id' => $existingMiracle->id]);
            DB::table('brands')->where('id', $aurea->id)
                ->update(['is_active' => false, 'updated_at' => now()]);
        } else {
            $slugTaken = DB::table('brands')->where('slug', 'miracle')
                ->where('id', '!=', $aurea->id)->exists();
            DB::table('brands')->where('id', $aurea->id)->update([
                'name'           => 'Miracle',
                'slug'           => $slugTaken ? ('miracle-' . $aurea->id) : 'miracle',
                'country_origin' => $aurea->country_origin ?: 'Colombia',
                'updated_at'     => now(),
            ]);
        }
    }

    public function down(): void
    {
        // Corrección de datos puntual: no se revierte.
    }
};
