<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Esmalte Miracle x15ml: corrige la MARCA en el contenido + pule SEO + limpia galeria.
 *
 * - La descripcion decia "de 15 ml de Belleza Aurea" (daba a entender que la MARCA es
 *   Belleza Aurea). La marca real es MIRACLE; Belleza Aurea es la tienda/distribuidora.
 * - Mejora meta_title/description/focus_keyword para rankear por "esmalte semipermanente
 *   miracle" y destacar los 60 tonos + la seleccion visual (ventaja vs miracle.com.co).
 * - Deja la galeria con solo la PORTADA: se habian subido 60 fotos de tono, pero ahora
 *   cada tono tiene su imagen de variante (migracion 000003), asi que la tira era redundante.
 *
 * Idempotente y acotado al producto por slug.
 */
return new class extends Migration
{
    public function up(): void
    {
        $product = DB::table('products')->where('slug', 'esmalte-semipermanente-15ml')->first();
        if (! $product) {
            return;
        }

        $descripcion = '<p>El <strong>esmalte semipermanente Miracle de 15 ml</strong> es un gel de alta pigmentación disponible en <strong>60 tonos</strong>: cubre parejo en dos capas y se cura en lámpara UV/LED para un acabado brillante que dura de 2 a 3 semanas sin descascararse.</p>'
            . '<ul>'
            . '<li>Alta cobertura y color intenso desde la primera pasada.</li>'
            . '<li>Acabado soak off: se retira fácil con removedor en gel, sin dañar la uña.</li>'
            . '<li>Rinde más que un esmalte tradicional: no se corre y seca al instante.</li>'
            . '<li>Ideal para uso profesional en salón y para manicura en casa.</li>'
            . '</ul>'
            . '<p>Elige tu tono en la paleta de colores y míralo al instante. Producto original <strong>Miracle</strong>, distribuido por Belleza Áurea con envío a toda Colombia y precios mayoristas y al detal.</p>';

        DB::table('products')->where('id', $product->id)->update([
            'meta_title'       => 'Esmalte Semipermanente Miracle x15 ml — 60 Tonos | Belleza Áurea',
            'meta_description' => 'Esmalte semipermanente Miracle 15 ml: 60 tonos de alta pigmentación, gel soak off UV/LED y duración de 2 a 3 semanas. Elige y mira tu tono. Envío a toda Colombia.',
            'focus_keyword'    => 'esmalte semipermanente miracle',
            'description'      => $descripcion,
            'updated_at'       => now(),
        ]);

        // Galeria: dejar solo la portada (primera imagen) si hay muchas (flood de tonos).
        $images = json_decode($product->images ?? '[]', true);
        if (is_array($images) && count($images) > 5) {
            DB::table('products')->where('id', $product->id)->update([
                'images'     => json_encode([$images[0]]),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        // Contenido/galeria: no se revierte automaticamente (no se guardan los valores previos).
    }
};
