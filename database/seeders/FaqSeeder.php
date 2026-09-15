<?php

namespace Database\Seeders;

use App\Models\Faq;
use App\Models\FaqCategory;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'name'  => 'Envíos',
                'slug'  => 'envios',
                'emoji' => '📦',
                'sort_order' => 1,
                'faqs'  => [
                    [
                        'question' => '¿Cuánto tarda mi pedido?',
                        'answer'   => 'En Bogotá los envíos llegan entre 24 y 48 horas hábiles después de confirmado el pago. Para el resto del país el tiempo estimado es de 2 a 5 días hábiles según la ciudad. Te enviaremos el número de guía por correo apenas la transportadora recoja tu pedido.',
                    ],
                    [
                        'question' => '¿Envían a todo Colombia?',
                        'answer'   => 'Sí, enviamos a todo el territorio nacional a través de nuestras transportadoras aliadas. Cubrimos capitales, municipios y zonas rurales. Si vives en una vereda alejada, escríbenos por WhatsApp y te confirmamos si llegamos.',
                    ],
                    [
                        'question' => '¿Cuál es el costo de envío?',
                        'answer'   => 'El costo se calcula automáticamente en el checkout según tu ciudad y peso del pedido. Además, todas las compras por encima del umbral configurado tienen envío gratis a nivel nacional (verás el monto exacto en la barra superior del carrito).',
                    ],
                    [
                        'question' => '¿Puedo rastrear mi pedido?',
                        'answer'   => 'Sí. Apenas despachamos tu pedido recibirás un correo con el número de guía y un enlace directo a la transportadora para hacer seguimiento en tiempo real. También puedes entrar a "Mi cuenta > Pedidos" y ver el estado actualizado.',
                    ],
                ],
            ],
            [
                'name'  => 'Pagos',
                'slug'  => 'pagos',
                'emoji' => '💳',
                'sort_order' => 2,
                'faqs'  => [
                    [
                        'question' => '¿Qué métodos de pago aceptan?',
                        'answer'   => 'Aceptamos tarjetas de crédito y débito (Visa, MasterCard, Amex), PSE con todos los bancos colombianos, Nequi, Daviplata, efectivo por Efecty/Baloto y transferencia bancaria directa. Todo integrado con ePayco para máxima seguridad.',
                    ],
                    [
                        'question' => '¿Es seguro pagar con tarjeta?',
                        'answer'   => 'Sí, 100% seguro. Los pagos se procesan a través de ePayco, una pasarela certificada con estándar PCI DSS. Nosotras nunca vemos ni almacenamos los datos de tu tarjeta; todo viaja cifrado directo al banco.',
                    ],
                    [
                        'question' => '¿Puedo pagar contra entrega?',
                        'answer'   => 'Por ahora no ofrecemos pago contra entrega, ya que trabajamos con transportadoras que no manejan recaudo. Todos los pagos se hacen de forma anticipada en el checkout — así garantizamos que tu pedido salga el mismo día.',
                    ],
                    [
                        'question' => '¿Cómo hago para transferencia?',
                        'answer'   => 'En el checkout selecciona "Transferencia bancaria" y verás nuestros datos bancarios. Haz la transferencia y sube el comprobante desde el enlace que te enviamos al correo. Verificamos y despachamos en menos de 12 horas hábiles.',
                    ],
                ],
            ],
            [
                'name'  => 'Devoluciones',
                'slug'  => 'devoluciones',
                'emoji' => '🔄',
                'sort_order' => 3,
                'faqs'  => [
                    [
                        'question' => '¿Puedo devolver un producto?',
                        'answer'   => 'Sí. Aceptamos devoluciones de productos sin abrir, sin usar y en su empaque original. Por temas de higiene, no aceptamos devoluciones de productos ya abiertos (esmaltes, cremas, sueros, etc.) salvo defecto de fábrica.',
                    ],
                    [
                        'question' => '¿Cuál es el plazo?',
                        'answer'   => 'Tienes hasta 5 días calendario desde que recibes tu pedido para solicitar la devolución, según la ley colombiana de retracto (Ley 1480 de 2011). Después de ese plazo solo podemos ayudarte si el producto tiene un defecto de fábrica.',
                    ],
                    [
                        'question' => '¿Cómo hago el proceso?',
                        'answer'   => 'Escríbenos por WhatsApp o al correo con tu número de pedido y una foto del producto. Coordinamos la recogida (el costo depende del motivo: gratis si fue error nuestro, a cargo tuyo si fue cambio de opinión) y una vez recibido y verificado te devolvemos el dinero por el mismo medio de pago en 5-10 días hábiles.',
                    ],
                ],
            ],
            [
                'name'  => 'Productos',
                'slug'  => 'productos',
                'emoji' => '💛',
                'sort_order' => 4,
                'faqs'  => [
                    [
                        'question' => '¿Los productos son cruelty-free?',
                        'answer'   => 'Sí, todas las marcas que curamos en Belleza Áurea son cruelty-free: no se prueban en animales en ninguna etapa de su fabricación. Puedes ver el sello en la ficha de cada producto.',
                    ],
                    [
                        'question' => '¿Cómo elijo el tono correcto?',
                        'answer'   => 'Cada producto que necesita elección de tono (bases, correctores, esmaltes) trae una guía visual con muestras reales de piel. También puedes hacer nuestro Quiz de piel para recibir una recomendación personalizada, o escribirnos por WhatsApp y te asesoramos.',
                    ],
                    [
                        'question' => '¿Tienen productos veganos?',
                        'answer'   => 'Sí, tenemos una selección amplia de productos 100% veganos (sin ingredientes de origen animal). Puedes filtrar el catálogo por "Vegano" o buscar el sello verde en las fichas de cada producto.',
                    ],
                    [
                        'question' => '¿Los productos tienen fecha de vencimiento?',
                        'answer'   => 'Todos nuestros productos son 100% originales y con fecha de vencimiento vigente por al menos 12 meses desde la compra. Puedes ver la fecha exacta impresa en el empaque de cada producto.',
                    ],
                ],
            ],
            [
                'name'  => 'Mayoristas',
                'slug'  => 'mayoristas',
                'emoji' => '🏪',
                'sort_order' => 5,
                'faqs'  => [
                    [
                        'question' => '¿Cómo ser distribuidora?',
                        'answer'   => 'Si tienes un salón, spa, centro de estética o quieres revender nuestros productos, entra a "Mi cuenta > Ser mayorista" o escríbenos por WhatsApp. Te enviamos el formulario, revisamos tus datos y activamos tu cuenta mayorista en 24-48 horas.',
                    ],
                    [
                        'question' => '¿Cuál es el descuento?',
                        'answer'   => 'Las clientas mayoristas aprobadas ven precios especiales en todo el catálogo (hasta 30% de descuento según la marca) y acceden a promociones exclusivas. El precio mayorista se muestra automáticamente al iniciar sesión con tu cuenta aprobada.',
                    ],
                    [
                        'question' => '¿Cuánto es el pedido mínimo?',
                        'answer'   => 'El pedido mínimo mayorista es de 300.000 COP por compra. No hay compromiso de volumen mensual: pides cuando lo necesites, en la cantidad que necesites (siempre por encima del mínimo).',
                    ],
                ],
            ],
        ];

        foreach ($data as $cat) {
            $faqs = $cat['faqs'];
            unset($cat['faqs']);

            $category = FaqCategory::updateOrCreate(
                ['slug' => $cat['slug']],
                $cat + ['is_active' => true],
            );

            foreach ($faqs as $index => $faq) {
                Faq::updateOrCreate(
                    [
                        'faq_category_id' => $category->id,
                        'question'        => $faq['question'],
                    ],
                    [
                        'answer'     => $faq['answer'],
                        'sort_order' => $index + 1,
                        'is_active'  => true,
                    ],
                );
            }
        }
    }
}
