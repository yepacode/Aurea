<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * PILOTO SEO "hub de categoría + por tono" para ESMALTES (ganarle a Miracle).
 *
 * 1) Convierte la categoría "esmaltes" en página-hub: meta + guía única de
 *    compra (contenido propio y más profundo que el de miracle.com.co, que
 *    solo tiene 2 párrafos genéricos).
 * 2) Re-categoriza los esmaltes dispersos a la categoría "esmaltes" (antes
 *    solo tenía 2 productos ⇒ hub vacío).
 * 3) Reescribe cada ficha con descripción ÚNICA + meta por tono (cola larga
 *    que Miracle no cubre porque mete 70 tonos en una sola URL).
 *
 * Idempotente y seguro: busca por slug; si la categoría o un producto no
 * existen, esa parte es no-op. Solo toca campos SEO/categoría.
 */
return new class extends Migration
{
    public function up(): void
    {
        $cat = DB::table('categories')->where('slug', 'esmaltes')->first();
        if (! $cat) {
            return; // sin categoría esmaltes no hay hub que poblar
        }

        // 1) HUB: categoría esmaltes --------------------------------------
        $guia = "En Belleza Áurea encuentras esmaltes semipermanentes profesionales de las mejores marcas —Miracle y Jan Beauty (certificado INVIMA)— en presentaciones de 10 y 15 ml y más de 55 tonos de alta pigmentación. Son esmaltes en gel que se curan en lámpara UV/LED, con acabado brillante y una duración de 2 a 3 semanas sin descascararse.\n\n¿Cómo elegir tu tono? Los nude y pastel (como el baby boomer) son ideales para un look natural y elegante; los vibrantes y oscuros lucen perfectos para diseños y temporada; y los efectos especiales —como el ojo de gato magnético— logran acabados llamativos en un solo paso.\n\nAplicación paso a paso: 1) Prepara la uña y aplica base rubber o base coat y cura. 2) Aplica dos capas finas del semipermanente, curando cada una en lámpara. 3) Sella con top coat y cura. 4) Limpia el residuo pegajoso. Para retirarlo, lima la capa brillante y usa removedor en gel: nunca lo arranques.\n\nSemipermanente vs. esmalte tradicional: el semipermanente dura mucho más, no se corre y seca al instante en lámpara, por eso es el favorito en salón y para uso profesional.\n\nTodos nuestros esmaltes son originales, con envío a toda Colombia y precios mayoristas y al detal.";

        DB::table('categories')->where('id', $cat->id)->update([
            'meta_title'       => 'Esmalte Semipermanente — Miracle, Jan Beauty y +55 Tonos | Belleza Áurea',
            'meta_description' => 'Esmaltes semipermanentes profesionales 10–15 ml: +55 tonos de alta pigmentación, acabado soak off UV/LED y larga duración. Marcas Miracle y Jan Beauty (INVIMA). Envío a toda Colombia.',
            'focus_keyword'    => 'esmalte semipermanente',
            'description'      => $guia,
            'updated_at'       => now(),
        ]);

        // 2) + 3) Re-categorizar y reescribir fichas por tono --------------
        $fichas = [
            'esmalte-semipermanente-15ml' => [
                'meta_title'       => 'Esmalte Semipermanente x15 ml — Alta Pigmentación | Belleza Áurea',
                'meta_description' => 'Esmalte semipermanente 15 ml de alta pigmentación: gel soak off UV/LED, cobertura total y duración de 2 a 3 semanas. Envío a toda Colombia.',
                'focus_keyword'    => 'esmalte semipermanente 15ml',
                'description'      => '<p>El <strong>esmalte semipermanente de 15 ml</strong> de Belleza Áurea es un gel de alta pigmentación que cubre parejo en dos capas y se cura en lámpara UV/LED para un acabado brillante que dura de 2 a 3 semanas sin descascararse.</p><ul><li>Alta cobertura y color intenso desde la primera pasada.</li><li>Acabado soak off: se retira fácil con removedor en gel, sin dañar la uña.</li><li>Rinde más que un esmalte tradicional: no se corre y seca al instante.</li><li>Ideal para uso profesional en salón y para manicura en casa.</li></ul><p>Combínalo con base rubber y top coat para máxima duración. Producto original con envío a toda Colombia.</p>',
            ],
            'esmalte-semipermanente-aureo' => [
                'meta_title'       => 'Esmalte Semipermanente Áureo — Gel UV/LED Larga Duración | Belleza Áurea',
                'meta_description' => 'Esmalte semipermanente Áureo: gel UV/LED de alta cobertura y brillo espejo que dura semanas sin descascarar. Acabado soak off. Envío a toda Colombia.',
                'focus_keyword'    => 'esmalte semipermanente áureo',
                'description'      => '<p>El <strong>Esmalte Semipermanente Áureo</strong> es nuestro gel de firma UV/LED: cobertura total, color uniforme y un brillo espejo que se mantiene impecable durante 2 a 3 semanas. Su fórmula soak off facilita el retiro sin maltratar la uña natural.</p><ul><li>Alta pigmentación: color pleno en dos capas finas.</li><li>Brillo duradero que no amarillea ni se opaca.</li><li>Se cura en segundos en lámpara UV o LED.</li><li>Perfecto para nail art y manicura profesional.</li></ul><p>Úsalo sobre base coat y séllalo con top coat para una duración máxima. Original, con envío a toda Colombia.</p>',
            ],
            'esmalte-semipermanente-baby-10ml' => [
                'meta_title'       => 'Esmalte Semipermanente Baby Boomer x10 ml — Tono Suave | Belleza Áurea',
                'meta_description' => 'Esmalte semipermanente baby boomer 10 ml: tono nude/pastel suave para una manicura delicada y elegante que dura semanas. Soak off UV/LED. Envío a Colombia.',
                'focus_keyword'    => 'esmalte semipermanente baby boomer',
                'description'      => '<p>El <strong>Esmalte Semipermanente Baby x10 ml</strong> ofrece ese tono nude tipo <em>baby boomer</em> tan pedido: suave, femenino y elegante, ideal para una manicura delicada que combina con todo y luce natural.</p><ul><li>Tono pastel de cobertura cremosa y pareja.</li><li>Acabado en gel UV/LED con brillo y larga duración (2–3 semanas).</li><li>Soak off: retiro fácil con removedor en gel.</li><li>Perfecto para degradados baby boomer y looks minimalistas.</li></ul><p>Presentación de 10 ml. Producto original con envío a toda Colombia.</p>',
            ],
            'esmalte-semipermanente-jan-beauty-x10ml-certificado-invima' => [
                'meta_title'       => 'Esmalte Semipermanente Jan Beauty x10 ml INVIMA — 55 Tonos | Belleza Áurea',
                'meta_description' => 'Esmalte semipermanente Jan Beauty 10 ml, certificado INVIMA: 55 tonos de alta pigmentación, gel soak off UV/LED y duración de hasta 3 semanas. Envío a Colombia.',
                'focus_keyword'    => 'esmalte semipermanente jan beauty',
                'description'      => '<p>El <strong>Esmalte Semipermanente Jan Beauty x10 ml</strong> está <strong>certificado por INVIMA</strong> y disponible en <strong>55 tonos</strong> de alta pigmentación. Su fórmula en gel se cura en lámpara UV/LED y mantiene el color intenso y brillante hasta 3 semanas.</p><ul><li>55 tonos: nudes, pasteles, vibrantes y oscuros.</li><li>Certificado INVIMA: calidad y seguridad respaldadas.</li><li>Alta cobertura en dos capas; no se corre ni descascara.</li><li>Soak off: retiro sencillo con removedor en gel.</li></ul><p>Elige tu tono favorito. Producto original con envío a toda Colombia y precios mayoristas.</p>',
            ],
            'esmalte-baby-dulce-pastel' => [
                'meta_title'       => 'Esmalte Baby Dulce — Tono Pastel Suave | Belleza Áurea',
                'meta_description' => 'Esmalte baby dulce en tono pastel: color suave y femenino de buena cobertura y brillo, ideal para una manicura delicada. Envío a toda Colombia.',
                'focus_keyword'    => 'esmalte tono pastel baby',
                'description'      => '<p>El <strong>Esmalte Baby Dulce</strong> es un tono pastel suave y femenino, de cobertura pareja y lindo brillo, perfecto para una manicura delicada y elegante que combina con cualquier look.</p><ul><li>Tono pastel tierno, muy favorecedor.</li><li>Buena cobertura: color uniforme sin dejar rayas.</li><li>Versátil para el día a día y ocasiones especiales.</li></ul><p>Producto original con envío a toda Colombia.</p>',
            ],
            'esmalte-ojo-de-gato-15ml' => [
                'meta_title'       => 'Esmalte Ojo de Gato x15 ml — Efecto Magnético | Belleza Áurea',
                'meta_description' => 'Esmalte ojo de gato 15 ml: gel magnético con efecto 3D tipo cat-eye. Alta pigmentación y acabado UV/LED duradero. Envío a toda Colombia.',
                'focus_keyword'    => 'esmalte ojo de gato',
                'description'      => '<p>El <strong>Esmalte Ojo de Gato x15 ml</strong> es un gel magnético que crea el llamativo efecto <em>cat-eye</em> en 3D: pasas el imán sobre la uña y aparece una franja de luz que se mueve con el reflejo.</p><ul><li>Efecto magnético 3D en un solo paso.</li><li>Alta pigmentación y brillo intenso.</li><li>Se cura en lámpara UV/LED; larga duración.</li><li>Ideal para diseños elegantes y de temporada.</li></ul><p>Aplica sobre base, usa el imán y sella con top coat. Original, con envío a toda Colombia.</p>',
            ],
        ];

        foreach ($fichas as $slug => $data) {
            DB::table('products')
                ->where('slug', $slug)
                ->update(array_merge($data, [
                    'category_id' => $cat->id,
                    'updated_at'  => now(),
                ]));
        }
    }

    public function down(): void
    {
        // Migración de contenido/SEO: no se revierte automáticamente (no se
        // guardan los textos ni la categoría anteriores). Revertir a mano si
        // hiciera falta.
    }
};
