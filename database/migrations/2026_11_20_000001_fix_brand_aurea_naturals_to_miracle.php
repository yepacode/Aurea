<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Corrige la marca de los productos actuales: estaban todos como "Áurea Naturals"
 * (nombre de la distribuidora), pero el fabricante real es "Miracle".
 * Belleza Áurea DISTRIBUYE Miracle; no es el fabricante. Esto evita además
 * problemas de "misrepresentation" en Google/Meta por marca incorrecta.
 *
 * A futuro, cada otra marca que se distribuya debe tener su propio registro en
 * `brands` y asignarse al producto; esta corrección solo toca la marca mal puesta.
 */
return new class extends Migration
{
    public function up(): void
    {
        $aurea = DB::table('brands')->where('name', 'Áurea Naturals')->first();
        if (! $aurea) {
            return; // nada que corregir
        }

        $existingMiracle = DB::table('brands')
            ->where('name', 'Miracle')
            ->where('id', '!=', $aurea->id)
            ->first();

        if ($existingMiracle) {
            // Ya hay una marca "Miracle": reasigna los productos a ella y desactiva la vieja.
            DB::table('products')->where('brand_id', $aurea->id)
                ->update(['brand_id' => $existingMiracle->id]);
            DB::table('brands')->where('id', $aurea->id)
                ->update(['is_active' => false, 'updated_at' => now()]);
        } else {
            // Renombra el registro mal puesto a la marca real.
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
        // Corrección de datos puntual: no se revierte automáticamente para no
        // reintroducir la marca incorrecta.
    }
};
