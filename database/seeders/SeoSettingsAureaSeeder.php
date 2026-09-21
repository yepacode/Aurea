<?php

namespace Database\Seeders;

use App\Models\SeoSetting;
use Illuminate\Database\Seeder;

/**
 * SEO nivel agencia para las páginas fijas del storefront Belleza Áurea.
 *
 * Ejecutar manualmente (NO se llama desde DatabaseSeeder):
 *   php artisan db:seed --class=SeoSettingsAureaSeeder
 *
 * Titles 50-60 chars · Descriptions 140-160 chars · Keywords intención Colombia.
 */
class SeoSettingsAureaSeeder extends Seeder
{
    public function run(): void
    {
        // Nota: los page_key ya usados por los controladores son:
        //   home, products-index, blue-light (rituales), brands, contact,
        //   shipping-returns, about, quiz. Los demás quedan cargados en BD
        //   listos para cuando el admin quiera editar o cuando se conecten.
        $data = [
            [
                'page_key' => 'home',
                'meta_title' => 'Belleza Áurea | Cosmética e insumos de belleza Colombia',
                'meta_description' => 'Descubre insumos y cosmética premium para tu ritual diario. Marcas curadas, envío a toda Colombia y rituales pensados para ti. Empieza tu belleza áurea.',
                'og_title' => 'Belleza Áurea — Tu ritual de belleza empieza aquí',
                'og_description' => 'Cosmética consciente, insumos profesionales y rituales curados. Envío a toda Colombia con envío gratis desde $150.000.',
                'twitter_title' => 'Belleza Áurea — Cosmética y ritual de belleza Colombia',
                'twitter_description' => 'Marcas curadas, envío a toda Colombia y rituales que celebran tu belleza única.',
                'meta_keywords' => 'cosmética Colombia, insumos belleza, ritual de belleza, cosmética natural, cuidado facial Colombia, distribuidora belleza, belleza áurea, tienda belleza online',
            ],
            [
                'page_key' => 'products-index',
                'meta_title' => 'Catálogo de belleza — Cosmética e insumos | Belleza Áurea',
                'meta_description' => 'Explora nuestro catálogo completo: cosmética, uñas, decoración y cuidado facial. Envío a toda Colombia y descuento en tu primera compra.',
                'og_title' => 'Catálogo Belleza Áurea — Cosmética premium Colombia',
                'og_description' => 'Cientos de productos curados para tu ritual: skincare, uñas, decoración, tocador y más. Envío gratis desde $150.000.',
                'twitter_title' => 'Catálogo de belleza — Belleza Áurea Colombia',
                'twitter_description' => 'Cosmética e insumos de belleza con envío a toda Colombia y descuento en tu primera compra.',
                'meta_keywords' => 'catálogo belleza, cosmética online Colombia, insumos uñas, decoración belleza, tienda belleza online, productos belleza premium',
            ],
            [
                'page_key' => 'brands',
                'meta_title' => 'Marcas de belleza que amamos | Belleza Áurea Colombia',
                'meta_description' => 'Conoce las marcas curadas por Belleza Áurea: naturales, artesanales y con historia. Cada una elegida para elevar tu ritual de belleza diario.',
                'og_title' => 'Marcas curadas por Belleza Áurea',
                'og_description' => 'Naturales, artesanales, con historia. Descubre las marcas que hacen posible tu ritual áureo.',
                'twitter_title' => 'Marcas curadas por Belleza Áurea Colombia',
                'twitter_description' => 'Marcas naturales, artesanales y con historia. Cada una elegida para tu ritual diario.',
                'meta_keywords' => 'marcas cosmética Colombia, marcas belleza naturales, cosmética artesanal, marcas exclusivas Áurea, marcas skincare Colombia',
            ],
            [
                'page_key' => 'blue-light',
                'meta_title' => 'Rituales de belleza — Consejos y guías | Belleza Áurea',
                'meta_description' => 'Aprende rituales de belleza, tips de cuidado facial, uñas y cabello. Contenido pensado para acompañarte en tu camino de belleza consciente.',
                'og_title' => 'Rituales de belleza — Guías y consejos Áurea',
                'og_description' => 'Rutinas paso a paso, tips y guías de expertas para elevar tu ritual diario.',
                'twitter_title' => 'Rituales de belleza — Belleza Áurea',
                'twitter_description' => 'Guías, tips y rutinas para acompañarte en tu camino de belleza consciente.',
                'meta_keywords' => 'rituales de belleza, tips cuidado facial, cuidado uñas, guías belleza, blog belleza Colombia, rutinas skincare',
            ],
            [
                'page_key' => 'bundles-index',
                'meta_title' => 'Kits y rituales completos — Ahorra en pack | Belleza Áurea',
                'meta_description' => 'Kits de belleza cuidadosamente curados: ritual completo, kit descubrimiento y más. Ahorra hasta 20% comprando el pack completo.',
                'og_title' => 'Kits & rituales completos — Belleza Áurea',
                'og_description' => 'Combos curados para tu ritual: skincare, uñas y más. Ahorro real comprando el pack.',
                'twitter_title' => 'Kits de belleza con descuento — Belleza Áurea',
                'twitter_description' => 'Ritual completo o kit descubrimiento — ahorra hasta 20% en cada pack.',
                'meta_keywords' => 'kits belleza Colombia, kit uñas, kit facial, ritual completo belleza, pack cosmética con descuento, kits skincare',
            ],
            [
                'page_key' => 'subscriptions-index',
                'meta_title' => 'Rituales mensuales — Tu belleza en autopiloto | Áurea',
                'meta_description' => 'Suscríbete y recibe tu ritual de belleza cada mes con hasta 20% de descuento. Pausa cuando quieras. Envío puntual a toda Colombia.',
                'og_title' => 'Rituales mensuales Áurea — belleza en autopiloto',
                'og_description' => 'Recibe tu ritual cada mes, ahorra hasta 20% y pausa cuando quieras.',
                'twitter_title' => 'Suscripción belleza Colombia — Áurea',
                'twitter_description' => 'Tu ritual cada mes con descuento exclusivo. Envío puntual a toda Colombia.',
                'meta_keywords' => 'suscripción belleza Colombia, ritual mensual, box belleza, cosmética por suscripción, ritual descubrimiento mensual',
            ],
            [
                'page_key' => 'contact',
                'meta_title' => 'Contacto — Estamos para ayudarte | Belleza Áurea',
                'meta_description' => 'Dudas o pedidos especiales: escríbenos por WhatsApp, correo o formulario. Te acompañamos en tu ritual de belleza. Atención horario Colombia.',
                'og_title' => 'Contacta a Belleza Áurea — WhatsApp y correo',
                'og_description' => 'Atención cercana en horario Colombia. Resolvemos tus dudas y pedidos especiales.',
                'twitter_title' => 'Contacto Belleza Áurea Colombia',
                'twitter_description' => 'Escríbenos por WhatsApp o correo — te acompañamos en tu ritual de belleza.',
                'meta_keywords' => 'contacto Belleza Áurea, WhatsApp belleza, atención cliente cosmética, distribuidora cosmética Colombia',
            ],
            [
                'page_key' => 'about',
                'meta_title' => 'Sobre Áurea — Belleza consciente hecha para ti | Colombia',
                'meta_description' => 'Belleza Áurea nace del amor por rituales conscientes. Curamos marcas y productos que celebran tu belleza única. Conoce nuestra historia.',
                'og_title' => 'Sobre Belleza Áurea — nuestra historia',
                'og_description' => 'Rituales conscientes, marcas curadas y un propósito claro: celebrar tu belleza única.',
                'twitter_title' => 'Sobre Belleza Áurea — belleza consciente Colombia',
                'twitter_description' => 'Nacimos del amor por los rituales conscientes. Conoce por qué existimos.',
                'meta_keywords' => 'sobre Belleza Áurea, quiénes somos, cosmética consciente, tienda belleza Colombia, historia Áurea',
            ],
            [
                'page_key' => 'shipping-returns',
                'meta_title' => 'Envíos y devoluciones — Compra con tranquilidad | Áurea',
                'meta_description' => 'Envíos a toda Colombia con Servientrega e Interrapidísimo. Envío gratis desde $150.000. Devoluciones fáciles hasta 8 días después de recibir.',
                'og_title' => 'Envíos y devoluciones — Belleza Áurea',
                'og_description' => 'Envío gratis desde $150.000, cobertura nacional y devoluciones sin dolores de cabeza.',
                'twitter_title' => 'Envíos y devoluciones Belleza Áurea',
                'twitter_description' => 'Cobertura nacional, envío gratis desde $150.000 y devoluciones hasta 8 días.',
                'meta_keywords' => 'envíos Colombia belleza, envío gratis cosmética, devoluciones cosmética, política envíos Áurea, Servientrega belleza',
            ],
            [
                'page_key' => 'faq',
                'meta_title' => 'Preguntas frecuentes — Todo lo que necesitas saber | Áurea',
                'meta_description' => 'Resolvemos tus dudas sobre pagos, envíos, devoluciones, productos y programa mayorista. Encuentra respuestas al instante o escríbenos por WhatsApp.',
                'og_title' => 'Preguntas frecuentes — Belleza Áurea',
                'og_description' => 'Pagos, envíos, devoluciones y programa mayorista: todas tus dudas resueltas.',
                'twitter_title' => 'FAQ Belleza Áurea — Preguntas frecuentes',
                'twitter_description' => 'Respuestas rápidas sobre pagos, envíos y devoluciones. O escríbenos por WhatsApp.',
                'meta_keywords' => 'preguntas frecuentes belleza, dudas envíos cosmética, ayuda tienda online, soporte Belleza Áurea',
            ],
            [
                'page_key' => 'terms',
                'meta_title' => 'Términos y condiciones | Belleza Áurea',
                'meta_description' => 'Términos de uso, condiciones de compra, garantía y responsabilidades de Belleza Áurea. Compra informada, segura y con respaldo.',
                'og_title' => 'Términos y condiciones — Belleza Áurea',
                'og_description' => 'Condiciones de compra, garantía y derechos como consumidora en Colombia.',
                'twitter_title' => 'Términos y condiciones — Áurea',
                'twitter_description' => 'Condiciones de uso y compra en Belleza Áurea.',
                'meta_keywords' => 'términos condiciones Áurea, política compra belleza, condiciones venta cosmética',
            ],
            [
                'page_key' => 'privacy',
                'meta_title' => 'Política de privacidad — Tus datos seguros | Áurea',
                'meta_description' => 'Cómo tratamos tus datos personales: recolección, uso, almacenamiento y tus derechos como titular en Colombia (Ley 1581/2012).',
                'og_title' => 'Política de privacidad — Belleza Áurea',
                'og_description' => 'Tratamos tus datos con transparencia bajo la Ley 1581 de 2012 (Habeas Data).',
                'twitter_title' => 'Política de privacidad — Áurea',
                'twitter_description' => 'Tus datos seguros y tratados conforme a Habeas Data Colombia.',
                'meta_keywords' => 'privacidad datos Colombia, habeas data belleza, política privacidad Áurea, Ley 1581',
            ],
            [
                'page_key' => 'cookies',
                'meta_title' => 'Política de cookies | Belleza Áurea',
                'meta_description' => 'Uso de cookies y tecnologías similares en Belleza Áurea. Cómo funcionan, para qué las usamos y cómo gestionarlas desde tu navegador.',
                'og_title' => 'Política de cookies — Belleza Áurea',
                'og_description' => 'Transparencia total sobre las cookies que usamos y cómo desactivarlas.',
                'twitter_title' => 'Política de cookies — Áurea',
                'twitter_description' => 'Cómo usamos las cookies en Belleza Áurea y cómo gestionarlas.',
                'meta_keywords' => 'política cookies Áurea, gestión cookies web, cookies tienda online Colombia',
            ],
            [
                'page_key' => 'wholesale',
                'meta_title' => 'Programa mayorista — Precios especiales | Belleza Áurea',
                'meta_description' => '¿Distribuidora o salón de belleza? Únete al programa mayorista Áurea: precios especiales, envío preferencial y atención directa.',
                'og_title' => 'Programa mayorista Belleza Áurea',
                'og_description' => 'Precios por mayor, atención directa y envío preferencial para tu negocio de belleza.',
                'twitter_title' => 'Mayoristas — Belleza Áurea Colombia',
                'twitter_description' => 'Precios especiales para distribuidoras y salones de belleza en Colombia.',
                'meta_keywords' => 'mayorista belleza Colombia, distribuidora cosmética, precios por mayor belleza, cosmética al por mayor, salón belleza insumos',
            ],
            [
                'page_key' => 'referrals',
                'meta_title' => 'Recomienda Áurea y gana puntos | Programa de referidos',
                'meta_description' => 'Comparte Áurea con tus amigas: ella recibe 500 puntos de bienvenida y tú también ganas. Referidos ilimitados. Es amistad que se premia.',
                'og_title' => 'Programa de referidos — Belleza Áurea',
                'og_description' => 'Comparte tu link, gana puntos cada vez que una amiga compra. Referidos ilimitados.',
                'twitter_title' => 'Referidas Belleza Áurea — gana puntos',
                'twitter_description' => 'Recomienda Áurea, ella gana 500 puntos y tú también. Amistad que se premia.',
                'meta_keywords' => 'referidos Belleza Áurea, gana puntos recomendando, programa referidas belleza, código descuento amigas',
            ],
            [
                'page_key' => 'quiz',
                'meta_title' => 'Quiz de belleza — Descubre tu ritual ideal | Áurea',
                'meta_description' => 'Responde 5 preguntas rápidas y te recomendamos el ritual de belleza ideal para tu tipo de piel, uñas y necesidades. Toma 30 segundos.',
                'og_title' => 'Quiz de belleza — encuentra tu ritual ideal',
                'og_description' => '30 segundos. 5 preguntas. Un ritual pensado para ti.',
                'twitter_title' => 'Quiz de belleza Áurea — tu ritual ideal',
                'twitter_description' => 'Descubre en 30 segundos qué productos necesita tu ritual de belleza.',
                'meta_keywords' => 'quiz belleza, test tipo piel, ritual belleza personalizado, cosmética para mí, quiz skincare Colombia',
            ],
        ];

        $created = 0;
        $updated = 0;
        foreach ($data as $row) {
            $existed = SeoSetting::where('page_key', $row['page_key'])->exists();
            SeoSetting::updateOrCreate(
                ['page_key' => $row['page_key']],
                array_merge($row, ['is_active' => true])
            );
            $existed ? $updated++ : $created++;
        }

        $this->command?->info("SeoSettings — creados: {$created} · actualizados: {$updated}");
    }
}
