<?php

namespace App\Services;

use App\Models\BlogPost;
use App\Models\Product;

class SeoService
{
    /**
     * Generate meta tags array for a page.
     *
     * @return array{title: string, description: string, og_title: string, og_description: string, og_image: string, canonical: string}
     */
    public function meta(string $title, string $description, ?string $image = null, ?string $canonical = null, string $ogType = 'website'): array
    {
        return [
            'title' => $title,
            'description' => $description,
            'canonical' => $canonical ?? url()->current(),
            'og_type' => $ogType,
            'og_title' => $title,
            'og_description' => $description,
            'og_image' => $image ?? asset('images/og-default.jpg'),
            'twitter_card' => 'summary_large_image',
            'twitter_title' => $title,
            'twitter_description' => $description,
            'twitter_image' => $image ?? asset('images/og-default.jpg'),
            'keywords' => null,
            // Guard multi-idioma (ver applyItemSeo): páginas /en/ salen noindex
            // mientras el contenido no esté traducido de verdad.
            'robots' => app()->getLocale() === 'en' ? 'noindex, follow' : 'index, follow',
            'custom_schema' => null,
        ];
    }

    /**
     * Aplica los overrides de SEO por-ítem (guardados desde el admin) sobre
     * el arreglo meta base: canónica, OG, Twitter, keywords, robots y JSON-LD.
     *
     * @param  object  $item   modelo con columnas SEO
     * @param  string  $ogCol  columna de imagen OG del modelo
     * @param  string  $twCol  columna de imagen Twitter del modelo
     */
    public function applyItemSeo(array $meta, object $item, string $ogCol = 'og_image_path', string $twCol = 'twitter_image_path'): array
    {
        if (! empty($item->canonical_url)) {
            $meta['canonical'] = $item->canonical_url;
        }
        if (! empty($item->og_type)) {
            $meta['og_type'] = $item->og_type;
        }
        if (! empty($item->og_title)) {
            $meta['og_title'] = $item->og_title;
        }
        if (! empty($item->og_description)) {
            $meta['og_description'] = $item->og_description;
        }
        if (! empty($item->{$ogCol})) {
            $og = asset('storage/'.$item->{$ogCol});
            $meta['og_image'] = $og;
            $meta['twitter_image'] = $og;
        }

        // Twitter: usa sus propios campos o cae al equivalente OG
        if (! empty($item->twitter_card)) {
            $meta['twitter_card'] = $item->twitter_card;
        }
        $meta['twitter_title'] = $item->twitter_title ?: $meta['og_title'];
        $meta['twitter_description'] = $item->twitter_description ?: $meta['og_description'];
        if (! empty($item->{$twCol})) {
            $meta['twitter_image'] = asset('storage/'.$item->{$twCol});
        }

        // Keywords: meta_keywords o, en su defecto, la palabra clave principal
        $kw = $item->meta_keywords ?? null;
        if (empty($kw) && ! empty($item->focus_keyword)) {
            $kw = $item->focus_keyword;
        }
        if (! empty($kw)) {
            $meta['keywords'] = $kw;
        }

        // Robots
        $index = ! empty($item->noindex) ? 'noindex' : 'index';
        $follow = ! empty($item->nofollow) ? 'nofollow' : 'follow';
        $meta['robots'] = "{$index}, {$follow}";

        // Guard multi-idioma: mientras el contenido /en/ NO esté traducido de
        // verdad (los meta/descs siguen en español), forzamos noindex para no
        // canibalizar la versión ES en el SERP. Se puede retirar este bloque
        // cuando los campos *_en estén poblados en producción.
        if (app()->getLocale() === 'en') {
            $meta['robots'] = 'noindex, ' . (! empty($item->nofollow) ? 'nofollow' : 'follow');
        }

        // JSON-LD personalizado
        if (! empty($item->custom_schema_markup)) {
            $meta['custom_schema'] = $item->custom_schema_markup;
        }

        return $meta;
    }

    /**
     * Generate meta tags for a product page.
     *
     * Garantiza fallbacks para title, description, og_image, canonical, keywords
     * y robots incluso si el admin no ha rellenado los campos SEO.
     */
    public function forProduct(Product $product): array
    {
        // Title: meta_title específico → nombre + marca
        $title = $product->meta_title ?: "{$product->name} | Belleza Áurea";

        // Description: meta_title específico → descripción recortada → fallback genérico
        $description = $product->meta_description
            ?: mb_substr(trim(strip_tags($product->description ?? '')), 0, 155);
        if (empty($description)) {
            $description = 'Descubre '.$product->name.' en Belleza Áurea. Insumos y cosmética profesional con envío a toda Colombia.';
        }

        // Imagen OG: og_image_path del admin → primera imagen del producto → logo de marca
        $ogImage = null;
        if (! empty($product->og_image_path)) {
            $ogImage = asset('storage/'.$product->og_image_path);
        } elseif (is_array($product->images) && ! empty($product->images[0])) {
            $ogImage = asset('storage/'.$product->images[0]);
        } else {
            $ogImage = asset('img/brand/logo-principal.png');
        }

        // Canonical respeta el idioma activo: en /en/productos/{slug} debe
        // apuntar a la versión EN, no a la ES (bug SEO real).
        $canonicalHelper = function_exists('locale_route') ? 'locale_route' : 'route';
        $meta = $this->meta(
            $title,
            $description,
            $ogImage,
            $canonicalHelper('products.show', ['slug' => $product->slug]),
            'product',
        );

        // applyItemSeo respeta prioridad: si hay valores específicos por-ítem
        // los usa; si no, mantiene los fallbacks calculados arriba.
        $meta = $this->applyItemSeo($meta, $product, 'og_image_path', 'twitter_image_path');

        // Keywords: focus_keyword > meta_keywords > categoría + nombre
        if (empty($meta['keywords'])) {
            $categoryName = $product->category?->name;
            $meta['keywords'] = $categoryName
                ? trim($categoryName.', '.$product->name)
                : $product->name;
        }

        // Robots: si el admin marcó noindex/nofollow ya lo aplicó applyItemSeo;
        // si no, index,follow por defecto (ya lo trae meta()).

        return $meta;
    }

    /**
     * Generate meta tags for a blog post.
     */
    public function forBlogPost(BlogPost $post): array
    {
        $title = $post->meta_title ?: "{$post->title} | Belleza Áurea";
        $description = $post->meta_description ?: mb_substr(strip_tags($post->excerpt ?? $post->content), 0, 160);
        $image = $post->image ? asset("storage/{$post->image}") : null;
        $canonical = $post->canonical_url ?: route('ritual.show', $post->slug);

        $meta = $this->meta($title, $description, $image, $canonical, 'article');

        return $this->applyItemSeo($meta, $post, 'og_image', 'twitter_image_path');
    }

    /**
     * Generate Organization schema for the home page.
     */
    public function organizationSchema(): string
    {
        return $this->toJsonLd($this->organizationData());
    }

    /**
     * Datos crudos de la Organization (sin envolver en <script>), para poder
     * reutilizarlos como nodo anidado (p. ej. mainEntity de AboutPage).
     *
     * @return array<string, mixed>
     */
    public function organizationData(): array
    {
        $legal = config('legal', []);
        $usable = fn ($v) => $v && ! \Illuminate\Support\Str::startsWith(trim((string) $v), '[');

        $data = [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => 'Belleza Áurea',
            'url' => url('/'),
            'logo' => asset('img/brand/logo-principal.png'),
            'description' => 'Distribuidora de insumos y cosmética profesional de belleza en Colombia: uñas, piel, maquillaje, cabello y estética.',
        ];

        // Punto de contacto (usa datos reales de config/legal.php cuando existan)
        $contact = ['@type' => 'ContactPoint', 'contactType' => 'customer service', 'availableLanguage' => 'Spanish'];
        if ($usable($legal['phone'] ?? null)) {
            $contact['telephone'] = $legal['phone'];
        }
        if ($usable($legal['email'] ?? null)) {
            $contact['email'] = $legal['email'];
        }
        $data['contactPoint'] = $contact;

        // Dirección
        if ($usable($legal['address'] ?? null) || $usable($legal['city'] ?? null)) {
            $data['address'] = array_filter([
                '@type' => 'PostalAddress',
                'streetAddress' => $usable($legal['address'] ?? null) ? $legal['address'] : null,
                'addressLocality' => $usable($legal['city'] ?? null) ? $legal['city'] : null,
                'addressCountry' => 'CO',
            ]);
        }

        // sameAs — perfiles sociales (desde la página de contacto en el admin)
        $contact = \App\Models\ContactPageSetting::getCurrent();
        $sameAs = array_values(array_filter([
            $contact->instagram_url ?? null,
            $contact->facebook_url ?? null,
            $contact->tiktok_url ?? null,
        ]));
        if (! empty($sameAs)) {
            $data['sameAs'] = $sameAs;
        }

        return $data;
    }

    /**
     * Generate rich Product schema (AI-ready / GEO) for a product page.
     *
     * Incluye brand, offers con priceValidUntil, additionalProperty para
     * cruelty-free/vegano/origen, GTIN/MPN, peso, ingredientes y howto
     * para que ChatGPT, Perplexity, Google AI Overviews y Bing Copilot
     * puedan citar el producto.
     */
    public function productSchema(Product $product): string
    {
        $brandName = $product->brand?->name ?? 'Belleza Áurea';
        $hasStock = $product->hasStock();
        $images = collect($product->images ?? [])
            ->map(fn ($img) => asset('storage/'.$img))
            ->all();

        $schema = [
            '@context'    => 'https://schema.org',
            '@type'       => 'Product',
            'name'        => $product->name,
            'description' => mb_substr(strip_tags($product->description ?? ''), 0, 5000),
            'url'         => route('products.show', $product->slug),
            'sku'         => $product->internal_code,
            'brand'       => [
                '@type' => 'Brand',
                'name'  => $brandName,
                'url'   => $product->brand
                    ? route('brands.show', $product->brand->slug)
                    : url('/'),
            ],
            'category'    => $product->category?->name,
            'offers'      => [
                '@type'         => 'Offer',
                'url'           => route('products.show', $product->slug),
                'priceCurrency' => 'COP',
                'price'         => number_format((float) $product->price, 2, '.', ''),
                'availability'  => $hasStock
                    ? 'https://schema.org/InStock'
                    : 'https://schema.org/OutOfStock',
                'itemCondition' => 'https://schema.org/NewCondition',
                'priceValidUntil' => now()->addYear()->toDateString(),
                'seller'        => [
                    '@type' => 'Organization',
                    'name'  => 'Belleza Áurea',
                    'url'   => url('/'),
                ],
                'shippingDetails' => [
                    '@type' => 'OfferShippingDetails',
                    'shippingRate' => [
                        '@type'    => 'MonetaryAmount',
                        'value'    => (string) (float) \App\Models\ShippingSetting::get('default_price', 0),
                        'currency' => 'COP',
                    ],
                    'shippingDestination' => [
                        '@type'          => 'DefinedRegion',
                        'addressCountry' => 'CO',
                    ],
                ],
                'hasMerchantReturnPolicy' => [
                    '@type'                => 'MerchantReturnPolicy',
                    'applicableCountry'    => 'CO',
                    'returnPolicyCategory' => 'https://schema.org/MerchantReturnFiniteReturnWindow',
                    'merchantReturnDays'   => (int) config('legal.retracto_dias', 5),
                    'returnMethod'         => 'https://schema.org/ReturnByMail',
                    'returnFees'           => 'https://schema.org/ReturnShippingFees',
                ],
            ],
        ];

        if (! empty($images)) {
            $schema['image'] = $images;
        }

        // Identificadores comerciales
        if ($product->gtin) $schema['gtin'] = $product->gtin;
        if ($product->mpn)  $schema['mpn']  = $product->mpn;

        // Peso / volumen (Schema acepta QuantitativeValue)
        if ($product->weight_value && $product->weight_unit) {
            $schema['weight'] = [
                '@type'    => 'QuantitativeValue',
                'value'    => (float) $product->weight_value,
                'unitText' => $product->weight_unit,
            ];
        }

        // País de origen (countryOfOrigin)
        if ($product->country_origin) {
            $schema['countryOfOrigin'] = [
                '@type' => 'Country',
                'name'  => $product->country_origin,
            ];
        }

        // Características adicionales como additionalProperty
        $additionalProps = [];
        if ($product->is_cruelty_free) {
            $additionalProps[] = ['@type' => 'PropertyValue', 'name' => 'Cruelty-free', 'value' => 'true'];
        }
        if ($product->is_vegan) {
            $additionalProps[] = ['@type' => 'PropertyValue', 'name' => 'Vegan', 'value' => 'true'];
        }
        if ($product->suitable_for) {
            $additionalProps[] = ['@type' => 'PropertyValue', 'name' => 'Suitable for', 'value' => $product->suitable_for];
        }
        if ($product->ingredients) {
            $additionalProps[] = ['@type' => 'PropertyValue', 'name' => 'Ingredients', 'value' => $product->ingredients];
        }
        if (! empty($additionalProps)) {
            $schema['additionalProperty'] = $additionalProps;
        }

        // Keywords para AI categorization
        if ($product->focus_keyword) {
            $schema['keywords'] = $product->focus_keyword;
        }

        // Reseñas → aggregateRating + review (estrellas en Google)
        $reviews = $product->relationLoaded('approvedReviews')
            ? $product->approvedReviews
            : $product->approvedReviews()->get();
        if ($reviews->count() > 0) {
            $schema['aggregateRating'] = [
                '@type'       => 'AggregateRating',
                'ratingValue' => round((float) $reviews->avg('rating'), 1),
                'reviewCount' => $reviews->count(),
                'bestRating'  => 5,
                'worstRating' => 1,
            ];
            $schema['review'] = $reviews->take(10)->map(fn ($r) => [
                '@type'         => 'Review',
                'reviewRating'  => ['@type' => 'Rating', 'ratingValue' => $r->rating, 'bestRating' => 5, 'worstRating' => 1],
                'author'        => ['@type' => 'Person', 'name' => $r->author_name],
                'datePublished' => $r->created_at?->toDateString(),
                'reviewBody'    => $r->comment,
            ])->values()->all();
        }

        return $this->toJsonLd($schema);
    }

    /**
     * HowTo schema separado — Google y los LLMs lo aman para "how to use".
     */
    public function howToSchema(Product $product): ?string
    {
        if (! $product->how_to_use) {
            return null;
        }

        // Si tiene saltos de línea o numeración tipo "1. ", los convertimos
        // en pasos. Si no, un paso único.
        $text = preg_replace('/\s*(?:\\\\n|\r\n|\r|\n)\s*/u', "\n", $product->how_to_use);
        $text = preg_replace('/(?<=[.;])\s+(?=\d+\.\s)/u', "\n", $text);
        $lines = collect(preg_split('/\n+/', $text))
            ->map(fn ($l) => trim(preg_replace('/^\d+\.\s*/', '', $l)))
            ->filter()
            ->values();

        $steps = $lines->map(fn ($l, $i) => [
            '@type'    => 'HowToStep',
            'position' => $i + 1,
            'name'     => 'Paso '.($i + 1),
            'text'     => $l,
        ])->all();

        $schema = [
            '@context'    => 'https://schema.org',
            '@type'       => 'HowTo',
            'name'        => 'Cómo usar '.$product->name,
            'description' => 'Modo de uso recomendado de '.$product->name,
            'step'        => $steps,
        ];

        return $this->toJsonLd($schema);
    }

    /**
     * Generate Article schema for a blog post.
     */
    public function articleSchema(BlogPost $post): string
    {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => $post->schema_type ?? 'BlogPosting',
            'headline' => $post->title,
            'description' => $post->meta_description ?? $post->excerpt ?? mb_substr(strip_tags($post->content), 0, 160),
            'url' => route('ritual.show', $post->slug),
            'datePublished' => $post->published_at?->toIso8601String(),
            'dateModified' => $post->updated_at->toIso8601String(),
            'author' => [
                '@type' => 'Organization',
                'name' => $post->author_name ?? 'Belleza Áurea',
                'url' => url('/'),
            ],
            'publisher' => [
                '@type' => 'Organization',
                'name' => 'Belleza Áurea',
                'logo' => [
                    '@type' => 'ImageObject',
                    'url' => asset('img/isotipo.png'),
                ],
            ],
            'mainEntityOfPage' => [
                '@type' => 'WebPage',
                '@id' => route('ritual.show', $post->slug),
            ],
        ];

        if ($post->image) {
            $schema['image'] = asset("storage/{$post->image}");
        }

        if ($post->focus_keyword) {
            $schema['keywords'] = $post->focus_keyword;
        }

        return $this->toJsonLd($schema);
    }

    /**
     * Generate FAQPage schema from an array of Q&A pairs.
     *
     * @param  array<int, array{question: string, answer: string}>  $faqs
     */
    public function faqSchema(array $faqs): string
    {
        return $this->toJsonLd([
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => array_map(fn ($faq) => [
                '@type' => 'Question',
                'name' => $faq['question'],
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => $faq['answer'],
                ],
            ], $faqs),
        ]);
    }

    /**
     * Generate BreadcrumbList schema.
     *
     * @param  array<int, array{name: string, url: string}>  $items
     */
    public function breadcrumbSchema(array $items): string
    {
        return $this->toJsonLd([
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => array_map(fn ($item, $index) => [
                '@type' => 'ListItem',
                'position' => $index + 1,
                'name' => $item['name'],
                'item' => $item['url'],
            ], $items, array_keys($items)),
        ]);
    }

    /**
     * ItemList genérico. Devuelve null si no hay elementos: un ItemList vacío
     * es peor que no emitirlo (Google lo marca como structured data inválido).
     *
     * @param  array<int, array<string, mixed>>  $items  objetos schema.org ya armados
     */
    public function itemListSchema(array $items, ?string $name = null): ?string
    {
        $items = array_values(array_filter($items));
        if (empty($items)) {
            return null;
        }

        $data = ['@context' => 'https://schema.org', '@type' => 'ItemList'];
        if ($name) {
            $data['name'] = $name;
        }
        $data['numberOfItems'] = count($items);
        $data['itemListElement'] = array_map(fn ($item, $i) => [
            '@type' => 'ListItem',
            'position' => $i + 1,
            'item' => $item,
        ], $items, array_keys($items));

        return $this->toJsonLd($data);
    }

    /**
     * ItemList de marcas (/marcas). Cada ítem es un Brand.
     *
     * @param  iterable<\App\Models\Brand>  $brands
     */
    public function brandListSchema(iterable $brands, ?string $name = null): ?string
    {
        $items = [];
        foreach ($brands as $brand) {
            $item = array_filter([
                '@type' => 'Brand',
                'name' => $brand->name,
                'url' => $this->localeUrl('brands.show', ['slug' => $brand->slug]),
                'logo' => $brand->logo_url ?: null,
                'description' => $brand->short_description ?: null,
            ], fn ($v) => $v !== null && $v !== '');
            $items[] = $item;
        }

        return $this->itemListSchema($items, $name);
    }

    /**
     * ItemList de productos. Cada ítem es un Product con su Offer en COP.
     *
     * @param  iterable<Product>  $products
     */
    public function productListSchema(iterable $products, ?string $name = null): ?string
    {
        $items = [];
        foreach ($products as $product) {
            $image = null;
            if (is_array($product->images) && ! empty($product->images[0])) {
                $image = asset('storage/'.$product->images[0]);
            }

            $items[] = array_filter([
                '@type' => 'Product',
                'name' => $product->name,
                'url' => $this->localeUrl('products.show', ['slug' => $product->slug]),
                'image' => $image,
                'offers' => [
                    '@type' => 'Offer',
                    'url' => $this->localeUrl('products.show', ['slug' => $product->slug]),
                    'price' => $this->schemaPrice($product->price),
                    'priceCurrency' => 'COP',
                    'availability' => $product->hasStock()
                        ? 'https://schema.org/InStock'
                        : 'https://schema.org/OutOfStock',
                ],
            ], fn ($v) => $v !== null && $v !== '');
        }

        return $this->itemListSchema($items, $name);
    }

    /**
     * ItemList de kits/bundles (/kits). Cada ítem es un Product con su Offer.
     *
     * @param  iterable<\App\Models\Bundle>  $bundles
     */
    public function bundleListSchema(iterable $bundles, ?string $name = null): ?string
    {
        $items = [];
        foreach ($bundles as $bundle) {
            $items[] = array_filter([
                '@type' => 'Product',
                'name' => $bundle->name,
                'url' => $this->localeUrl('bundles.show', ['slug' => $bundle->slug]),
                'image' => $bundle->image_url ?: null,
                'description' => $bundle->description
                    ? mb_substr(trim(strip_tags($bundle->description)), 0, 500)
                    : null,
                'offers' => [
                    '@type' => 'Offer',
                    'url' => $this->localeUrl('bundles.show', ['slug' => $bundle->slug]),
                    'price' => $this->schemaPrice($bundle->price),
                    'priceCurrency' => 'COP',
                    'availability' => 'https://schema.org/InStock',
                ],
            ], fn ($v) => $v !== null && $v !== '');
        }

        return $this->itemListSchema($items, $name);
    }

    /**
     * LocalBusiness (subtipo HealthAndBeautyBusiness) para /contacto.
     *
     * Sólo emite lo que existe de verdad: la dirección se omite si no hay
     * datos cargados, y el teléfono sale del WhatsApp configurado en el admin.
     */
    public function localBusinessSchema(?\App\Models\ContactPageSetting $contact = null): string
    {
        $contact = $contact ?: \App\Models\ContactPageSetting::getCurrent();
        $legal = config('legal', []);
        $usable = fn ($v) => $v && ! \Illuminate\Support\Str::startsWith(trim((string) $v), '[');

        $data = [
            '@context' => 'https://schema.org',
            '@type' => 'HealthAndBeautyBusiness',
            'name' => 'Belleza Áurea',
            'url' => url('/'),
            'logo' => asset('img/brand/logo-principal.png'),
            'image' => asset('img/brand/logo-principal.png'),
            'description' => 'Distribuidora de insumos y cosmética profesional de belleza en Colombia: uñas, piel, maquillaje, cabello y estética.',
        ];

        // Teléfono: WhatsApp del admin → teléfono del admin → config/legal.php.
        // Se normaliza a E.164 cuando sólo tenemos dígitos.
        $phone = null;
        $digits = $contact->whatsappDigits();
        if ($digits !== '') {
            $phone = '+'.$digits;
        } elseif ($usable($contact->phone ?? null)) {
            $phone = $contact->phone;
        } elseif ($usable($legal['phone'] ?? null)) {
            $phone = $legal['phone'];
        }
        if ($phone) {
            $data['telephone'] = $phone;
        }

        if ($usable($contact->email ?? null)) {
            $data['email'] = $contact->email;
        } elseif ($usable($legal['email'] ?? null)) {
            $data['email'] = $legal['email'];
        }

        // Dirección: SOLO si hay datos reales. Nada inventado.
        // ContactPageSetting no tiene columna de dirección hoy; si se agrega,
        // manda ella y si no caemos a config/legal.php (misma fuente que lo legal).
        $street = $usable($contact->address ?? null)
            ? $contact->address
            : ($usable($legal['address'] ?? null) ? $legal['address'] : null);
        $city = $usable($contact->city ?? null)
            ? $contact->city
            : ($usable($legal['city'] ?? null) ? $legal['city'] : null);

        // Si "address" es en realidad sólo la ciudad/país (p. ej. "Bucaramanga,
        // Colombia") no la publicamos como streetAddress: sería un dato falso.
        if ($street && $city) {
            $rest = str_ireplace([$city, (string) ($legal['country'] ?? '')], '', $street);
            if (trim(preg_replace('/[\s,.\-]+/u', '', $rest)) === '') {
                $street = null;
            }
        }

        if ($street || $city) {
            $data['address'] = array_filter([
                '@type' => 'PostalAddress',
                'streetAddress' => $street,
                'addressLocality' => $city,
                'addressCountry' => 'CO',
            ]);
        }

        // Horario de atención desde whatsapp_hours_json (7 días con open/close/enabled)
        $hoursMap = [
            'mon' => 'https://schema.org/Monday',
            'tue' => 'https://schema.org/Tuesday',
            'wed' => 'https://schema.org/Wednesday',
            'thu' => 'https://schema.org/Thursday',
            'fri' => 'https://schema.org/Friday',
            'sat' => 'https://schema.org/Saturday',
            'sun' => 'https://schema.org/Sunday',
        ];
        $opening = [];
        foreach ($contact->whatsappHours() as $day => $slot) {
            if (empty($slot['enabled']) || ! isset($hoursMap[$day])) {
                continue;
            }
            $opening[] = [
                '@type' => 'OpeningHoursSpecification',
                'dayOfWeek' => $hoursMap[$day],
                'opens' => $slot['open'],
                'closes' => $slot['close'],
            ];
        }
        if (! empty($opening)) {
            $data['openingHoursSpecification'] = $opening;
        }

        $data['areaServed'] = ['@type' => 'Country', 'name' => 'Colombia'];

        $sameAs = array_values(array_filter([
            $contact->instagram_url ?? null,
            $contact->facebook_url ?? null,
            $contact->tiktok_url ?? null,
        ]));
        if (! empty($sameAs)) {
            $data['sameAs'] = $sameAs;
        }

        return $this->toJsonLd($data);
    }

    /**
     * ContactPage para /contacto.
     */
    public function contactPageSchema(): string
    {
        return $this->toJsonLd([
            '@context' => 'https://schema.org',
            '@type' => 'ContactPage',
            'name' => 'Contacto | Belleza Áurea',
            'url' => $this->localeUrl('contact'),
            'inLanguage' => app()->getLocale(),
            'isPartOf' => [
                '@type' => 'WebSite',
                'name' => 'Belleza Áurea',
                'url' => url('/'),
            ],
        ]);
    }

    /**
     * AboutPage para /sobre-nosotras, con mainEntity → Organization.
     */
    public function aboutPageSchema(?string $description = null): string
    {
        $organization = $this->organizationData();
        unset($organization['@context']);

        $data = [
            '@context' => 'https://schema.org',
            '@type' => 'AboutPage',
            'name' => 'Sobre nosotras | Belleza Áurea',
            'url' => $this->localeUrl('about'),
            'inLanguage' => app()->getLocale(),
        ];
        if ($description) {
            $data['description'] = $description;
        }
        $data['mainEntity'] = $organization;

        return $this->toJsonLd($data);
    }

    /**
     * Blog con sus blogPost[] para el índice de /rituales.
     * Devuelve null si no hay posts publicados.
     *
     * @param  iterable<BlogPost>  $posts
     */
    public function blogSchema(iterable $posts, ?string $name = null, ?string $url = null): ?string
    {
        $entries = [];
        foreach ($posts as $post) {
            $entries[] = array_filter([
                '@type' => 'BlogPosting',
                'headline' => $post->title,
                'url' => $this->localeUrl('ritual.show', ['slug' => $post->slug]),
                'image' => $post->image ? asset('storage/'.$post->image) : null,
                'datePublished' => $post->published_at?->toIso8601String(),
                'dateModified' => $post->updated_at?->toIso8601String(),
            ], fn ($v) => $v !== null && $v !== '');
        }

        if (empty($entries)) {
            return null;
        }

        return $this->toJsonLd([
            '@context' => 'https://schema.org',
            '@type' => 'Blog',
            'name' => $name ?: 'Rituales de belleza | Belleza Áurea',
            'url' => $url ?: $this->localeUrl('blue-light'),
            'inLanguage' => app()->getLocale(),
            'publisher' => [
                '@type' => 'Organization',
                'name' => 'Belleza Áurea',
                'url' => url('/'),
                'logo' => [
                    '@type' => 'ImageObject',
                    'url' => asset('img/brand/logo-principal.png'),
                ],
            ],
            'blogPost' => $entries,
        ]);
    }

    /**
     * URL absoluta respetando el idioma activo (igual que el resto del front).
     */
    public function localeUrl(string $routeName, array $params = []): string
    {
        return function_exists('locale_route')
            ? locale_route($routeName, $params)
            : route($routeName, $params);
    }

    /**
     * Precio tal y como lo exige schema.org: numérico, punto decimal,
     * sin separador de miles y sin símbolo de moneda.
     */
    private function schemaPrice($price): string
    {
        return number_format((float) $price, 2, '.', '');
    }

    /**
     * Encode schema data as a JSON-LD script tag.
     */
    private function toJsonLd(array $data): string
    {
        $json = json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

        return "<script type=\"application/ld+json\">\n{$json}\n</script>";
    }
}
