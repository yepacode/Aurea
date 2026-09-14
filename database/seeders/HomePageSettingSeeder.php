<?php

namespace Database\Seeders;

use App\Models\HomePageSetting;
use Illuminate\Database\Seeder;

class HomePageSettingSeeder extends Seeder
{
    public function run(): void
    {
        HomePageSetting::updateOrCreate(['id' => 1], [
            // ── Categorías ──
            'categories_label' => 'Categorías',
            'categories_title' => 'Encuentra tu ritual ideal',
            'categories_subtitle' => 'Skincare, fragancias y sets pensados para cada momento de tu día.',
            'category_cards' => [
                [
                    'name' => 'Skincare',
                    'link_param' => 'sin_graduacion',
                    'description' => 'Sérums, cremas, tónicos y mascarillas con ingredientes botánicos.',
                    'icon_svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 3c2.5 3 4 5 4 8a4 4 0 1 1-8 0c0-3 1.5-5 4-8Z"/>',
                ],
                [
                    'name' => 'Fragancias',
                    'link_param' => 'sin_graduacion',
                    'description' => 'Perfumes con notas florales, cítricas y ámbar dorado. Larga duración.',
                    'icon_svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 4h6v3H9V4Zm-1 3h8l-1 14H9L8 7Z"/>',
                ],
                [
                    'name' => 'Rituales',
                    'link_param' => 'toallitas',
                    'description' => 'Sets seleccionados para tu rutina diaria. Todo lo que necesitas en uno.',
                    'icon_svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 7h16v12H4V7Zm4-3h8v3H8V4Z"/>',
                ],
            ],

            // ── Catálogo ──
            'catalog_label' => 'Catálogo',
            'catalog_title' => 'Nuestros productos',
            'catalog_subtitle' => 'Formulaciones limpias, packaging cuidado, envío en 48 h.',

            // ── Promo destacada (un producto estrella) ──
            'promo_label' => 'Producto estrella',
            'promo_title' => 'Sérum Áureo con vitamina C',
            'promo_description' => 'Ilumina, unifica y suaviza líneas finas en 28 días. Textura ligera con vitamina C estabilizada y aceite de rosa mosqueta.',
            'promo_price' => '$580.00',
            'promo_price_note' => '30 ml · uso diario · piel sensible',
            'promo_btn_text' => 'Descubrir el ritual',

            // ── Benefits ──
            'benefits_label' => 'Por qué elegirnos',
            'benefits_title' => 'Belleza con propósito',
            'benefits_subtitle' => 'Cada producto pasa por un proceso cuidado, desde el campo hasta tu piel.',
            'benefits_cards' => [
                [
                    'title' => 'Ingredientes botánicos',
                    'description' => 'Activos naturales seleccionados, libres de parabenos y sulfatos agresivos.',
                ],
                [
                    'title' => 'Formulaciones limpias',
                    'description' => 'Sin pruebas en animales, packaging reciclable y trazabilidad completa.',
                ],
                [
                    'title' => 'Resultados visibles',
                    'description' => 'Activos en concentraciones efectivas. Piel más luminosa en 28 días.',
                ],
                [
                    'title' => 'Ritual atemporal',
                    'description' => 'Estética sobria, gestos cuidados. Tu rutina diaria como un acto de elegancia.',
                ],
            ],

            // ── Sección Sets ──
            'wipes_label' => 'Sets',
            'wipes_title' => 'Rituales completos',
            'wipes_description' => 'Lleva tu rutina al siguiente nivel con nuestros sets premium. Empacados en cajas de regalo doradas, listos para disfrutar o sorprender.',
            'wipes_features' => [
                'Ahorra hasta 25% vs comprar por separado',
                'Empaque regalo con detalle dorado',
                'Combinan productos que se potencian entre sí',
                'Tarjeta personalizada opcional',
            ],

            // ── FAQ ──
            'faqs' => [
                ['q' => '¿Para qué tipo de piel son los productos?', 'a' => 'Toda nuestra línea está formulada para ser tolerada por piel sensible, normal, mixta y seca. Cada producto indica si es especialmente recomendado para alguna preocupación específica (luminosidad, hidratación, antiedad).'],
                ['q' => '¿Hacen pruebas en animales?', 'a' => 'No. Todos los productos de Belleza Áurea son cruelty-free. Nuestros activos se prueban mediante ensayos in vitro y paneles voluntarios humanos.'],
                ['q' => '¿Cuánto tarda el envío?', 'a' => 'El envío estándar tarda de 2 a 4 días hábiles. Envío gratis desde $150.000. Empacamos cada pedido en cajas reutilizables con papel de seda.'],
                ['q' => '¿Puedo devolver un producto?', 'a' => 'Sí. Tienes 30 días para devolución sin costo si el producto no cumple tus expectativas. Solo te pedimos que lo envíes en su empaque original.'],
                ['q' => '¿Cómo sé qué productos elegir?', 'a' => 'Tenemos un Quiz de Piel guiado que en 90 segundos te recomienda los productos ideales según tu tipo de piel y tus objetivos.'],
            ],

            // ── Trust badges ──
            'trust_badges' => [
                [
                    'icon_svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25"/>',
                    'title' => 'Envío gratis',
                    'description' => 'Desde $150.000',
                ],
                [
                    'icon_svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12.75 11.25 15 15 9.75"/>',
                    'title' => 'Cruelty-free',
                    'description' => 'Cero pruebas en animales',
                ],
                [
                    'icon_svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16.023 9.348h4.992m-4.992 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7"/>',
                    'title' => '30 días',
                    'description' => 'Devolución sin costo',
                ],
                [
                    'icon_svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 8.25h19.5"/>',
                    'title' => 'Pago seguro',
                    'description' => 'Stripe encriptado',
                ],
            ],

            // ── CTA final ──
            'cta_title' => '¿Lista para tu ritual áureo?',
            'cta_subtitle' => 'Descubre tu rutina ideal en 90 segundos con el Quiz de Piel o explora nuestra colección completa.',
            'cta_btn_primary_text' => 'Comenzar quiz',
            'cta_btn_secondary_text' => 'Ver productos',
            'cta_trust_items' => [
                'Envío gratis desde $150.000',
                'Cruelty-free',
                '30 días de devolución',
            ],

            // ── Comparativo (Con vs Sin ritual) ──
            'comparison_label' => 'El antes y después',
            'comparison_title' => 'Con vs. sin tu ritual áureo',
            'comparison_subtitle' => 'Lo que cambia cuando incorporas Belleza Áurea a tu rutina.',
            'comparison_without_label' => 'Sin ritual',
            'comparison_without_items' => [
                'Piel apagada al despertar',
                'Líneas marcadas por deshidratación',
                'Tono desigual y poros visibles',
                'Sensación de tirantez al final del día',
            ],
            'comparison_with_label' => 'Con Belleza Áurea',
            'comparison_with_items' => [
                'Piel luminosa desde el primer uso',
                'Hidratación 24h con activos botánicos',
                'Tono unificado en 28 días',
                'Sensación de confort y suavidad continua',
            ],

            'is_active' => true,
        ]);
    }
}
