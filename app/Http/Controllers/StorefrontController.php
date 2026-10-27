<?php

namespace App\Http\Controllers;

use App\Helpers\ColorHelper;
use App\Models\BlogPost;
use App\Models\BlueLightPageSetting;
use App\Models\ContactPageSetting;
use App\Models\HeroSetting;
use App\Models\HomePageSetting;
use App\Models\ShippingReturnsPageSetting;
use App\Models\InfographicImage;
use App\Models\SeoSetting;
use App\Models\Category;
use App\Models\Testimonial;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\SeoService;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class StorefrontController extends Controller
{
    public function __construct(
        private SeoService $seo,
    ) {}

    public function home(): View
    {
        // Cache 5 min de todo lo que no cambia por request. heroProduct queda
        // FUERA del cache para preservar la rotación aleatoria en cada visita.
        $data = Cache::remember('storefront.home.v1.'.app()->getLocale(), 300, function () {
            return $this->buildHomeData();
        });

        // heroProduct — rota aleatoriamente por request; el ID fijo (star_product_id)
        // sí se cachea (dentro de buildHomeData) para no volver a consultarlo cada vez.
        $heroProduct = $this->resolveHeroProduct($data['starProduct'] ?? null);
        unset($data['starProduct']);

        return view('storefront.home', array_merge($data, compact('heroProduct')));
    }

    /**
     * Extrae toda la data cacheable del home (todo menos heroProduct aleatorio).
     */
    private function buildHomeData(): array
    {
        $lentes = Product::active()
            ->where(fn ($q) => $q->whereJsonContains('type', 'miopia')
                ->orWhereJsonContains('type', 'lectura')
                ->orWhereJsonContains('type', 'sin_graduacion'))
            ->where(function ($q) {
                $q->where('stock', '>', 0)
                  ->orWhereHas('variants', fn ($v) => $v->where('is_active', true)->where('stock', '>', 0));
            })
            ->whereNotNull('images')
            ->whereRaw('JSON_LENGTH(images) > 0')
            ->with(['variants', 'category', 'brand'])
            ->orderByDesc('is_featured')
            ->orderBy('sort_order')
            ->orderByDesc('created_at')
            ->get()
            ->filter(fn ($p) => $p->hasStock())
            ->values()
            ->take(8);

        $toallitas = Product::active()
            ->whereJsonContains('type', 'toallitas')
            ->where(function ($q) {
                $q->where('stock', '>', 0)
                  ->orWhereHas('variants', fn ($v) => $v->where('is_active', true)->where('stock', '>', 0));
            })
            ->whereNotNull('images')
            ->whereRaw('JSON_LENGTH(images) > 0')
            ->with('variants')
            ->orderBy('sort_order')
            ->get()
            ->filter(fn ($p) => $p->hasStock())
            ->values();

        $coloresDisponibles = ProductVariant::where('is_active', true)
            ->where('stock', '>', 0)
            ->whereNotNull('color')
            ->distinct()
            ->pluck('color')
            ->sort()
            ->values();

        $recentPosts = BlogPost::published()
            ->orderByDesc('published_at')
            ->limit(3)
            ->get();

        $infographics = InfographicImage::active()
            ->orderBy('sort_order')
            ->get();

        $organizationSchema = $this->seo->organizationSchema();

        $hero = HeroSetting::getCurrent();
        $homePage = HomePageSetting::getCurrent();
        $seoSettings = SeoSetting::getForPage('home');

        $faqSchema = $this->seo->faqSchema(
            collect($homePage->faqs ?? [])->map(fn ($faq) => [
                'question' => $faq['q'] ?? '',
                'answer' => $faq['a'] ?? '',
            ])->all()
        );

        // Producto estrella FIJO (star_product_id): resuelto aquí para cachear
        // el candidato. El aleatorio se resuelve fuera del cache (ver home()).
        $starProduct = null;
        if ($homePage->star_product_id) {
            $starProduct = Product::active()
                ->where('id', $homePage->star_product_id)
                ->whereNotNull('images')
                ->whereRaw('JSON_LENGTH(images) > 0')
                ->whereNotNull('description')
                ->where('description', '!=', '')
                ->where('price', '>', 0)
                ->with('variants')
                ->first();
        }

        // Determinar modo del hero
        $heroMode = 'split';
        if ($hero && $hero->media_type === 'video'
            && $hero->media_path
            && \Storage::disk('public')->exists($hero->media_path)) {
            $heroMode = 'video';
        }

        // Categorías para el home: ordenadas por sort_order (editable en admin),
        // con conteo de productos activos. Limit 8 para el grid del home.
        $categories = Category::withCount(['products' => fn ($q) => $q->where('is_active', true)])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->take(8)
            ->get();
        $testimonials = Testimonial::where('is_active', true)->orderBy('sort_order')->get();
        $featuredBrands = \App\Models\Brand::active()->featured()->ordered()->get();

        // Kits destacados — primeros 3 activos por sort_order.
        $featuredBundles = \App\Models\Bundle::active()
            ->with(['items' => fn ($q) => $q->select('products.id', 'name', 'slug', 'images', 'price')])
            ->orderBy('sort_order')
            ->orderByDesc('created_at')
            ->take(3)
            ->get();

        // Más vendidos — primero ventas ONLINE reales (con badge "#N en ventas"),
        // luego se rellena hasta 8 con ganadores curados / destacados / recientes
        // (sin badge de ventas). Ver buildBestSellers().
        $bestSellers = $this->buildBestSellers(8);

        return compact(
            'hero', 'starProduct', 'heroMode', 'homePage', 'seoSettings',
            'lentes', 'toallitas', 'coloresDisponibles',
            'recentPosts', 'infographics', 'organizationSchema', 'faqSchema',
            'categories', 'testimonials', 'featuredBrands', 'featuredBundles',
            'bestSellers',
        );
    }

    /**
     * Construye la lista de "Más vendidos" del home garantizando hasta $target ítems:
     *   1) ventas ONLINE reales (pedidos pagados) → llevan $p->sales_rank (badge "#N en ventas")
     *   2) ganadores curados por internal_code/miracle_ref (config/bestsellers.php) → sin badge
     *   3) destacados (is_featured) → sin badge
     *   4) recientes → relleno final sin badge
     * Dedup por id; cada producto queda con $p->sales_rank (int real o null).
     */
    private function buildBestSellers(int $target = 8): \Illuminate\Support\Collection
    {
        $withImgStock = function ($q) {
            $q->whereNotNull('images')
              ->whereRaw('JSON_LENGTH(images) > 0')
              ->where(function ($s) {
                  $s->where('stock', '>', 0)
                    ->orWhereHas('variants', fn ($v) => $v->where('is_active', true)->where('stock', '>', 0));
              });
        };
        $load = ['variants', 'category', 'brand'];

        // 1) Ventas ONLINE reales (pedidos pagados) → con badge "#N en ventas".
        $ids = Product::bestSellerIds($target);
        $result = collect();
        if ($ids->isNotEmpty()) {
            $result = Product::active()->whereIn('id', $ids)->where($withImgStock)
                ->with($load)->get()
                ->filter(fn ($p) => $p->hasStock())
                ->sortBy(fn ($p) => $ids->search($p->id))
                ->values()->take($target);
            $result->each(fn ($p, $i) => $p->sales_rank = $i + 1);
        }

        // 2) Ganadores curados por código (config), en el orden de la lista.
        //    Se emparejan solo por las columnas de código que EXISTAN en la tabla
        //    (internal_code / miracle_ref), para no romper si una no está migrada.
        if ($result->count() < $target) {
            $codes = collect(config('bestsellers.curated', []))->map(fn ($c) => (string) $c)->values();
            $matchCols = collect(['internal_code', 'miracle_ref'])
                ->filter(fn ($c) => \Illuminate\Support\Facades\Schema::hasColumn('products', $c))
                ->values();
            if ($codes->isNotEmpty() && $matchCols->isNotEmpty()) {
                $curated = Product::active()
                    ->whereNotIn('id', $result->pluck('id')->all())
                    ->where(function ($w) use ($codes, $matchCols) {
                        foreach ($matchCols as $idx => $col) {
                            $idx === 0
                                ? $w->whereIn($col, $codes->all())
                                : $w->orWhereIn($col, $codes->all());
                        }
                    })
                    ->where($withImgStock)->with($load)->limit(80)->get()
                    ->filter(fn ($p) => $p->hasStock())
                    ->sortBy(function ($p) use ($codes, $matchCols) {
                        return $matchCols->map(function ($col) use ($codes, $p) {
                            $k = $codes->search((string) ($p->{$col} ?? ''));
                            return $k === false ? PHP_INT_MAX : $k;
                        })->min();
                    })
                    ->values()->take($target - $result->count());
                $curated->each(fn ($p) => $p->sales_rank = null);
                $result = $result->concat($curated);
            }
        }

        // 3) Destacados (is_featured) → sin badge.
        if ($result->count() < $target) {
            $featured = Product::active()->where('is_featured', true)
                ->whereNotIn('id', $result->pluck('id')->all())
                ->where($withImgStock)->with($load)
                ->orderBy('sort_order')->orderByDesc('created_at')->limit(24)->get()
                ->filter(fn ($p) => $p->hasStock())
                ->values()->take($target - $result->count());
            $featured->each(fn ($p) => $p->sales_rank = null);
            $result = $result->concat($featured);
        }

        // 4) Recientes → relleno final para garantizar la fila.
        if ($result->count() < $target) {
            $recent = Product::active()
                ->whereNotIn('id', $result->pluck('id')->all())
                ->where($withImgStock)->with($load)
                ->orderByDesc('created_at')->limit(24)->get()
                ->filter(fn ($p) => $p->hasStock())
                ->values()->take($target - $result->count());
            $recent->each(fn ($p) => $p->sales_rank = null);
            $result = $result->concat($recent);
        }

        return $result->take($target)->values();
    }

    /**
     * heroProduct: si hay star_product_id se respeta; si no, aleatorio por request.
     */
    private function resolveHeroProduct($starProduct): ?Product
    {
        if ($starProduct) {
            return $starProduct;
        }

        return Product::active()
            ->where('stock', '>', 0)
            ->whereNotNull('images')
            ->whereRaw('JSON_LENGTH(images) > 0')
            ->whereNotNull('description')
            ->where('description', '!=', '')
            ->where('price', '>', 0)
            ->with('variants')
            ->inRandomOrder()
            ->first();
    }

    public function blueLight(): View
    {
        $blueLightPage = BlueLightPageSetting::getCurrent();

        $faqItems = collect($blueLightPage->faqs ?? [])->map(fn ($faq) => [
            'question' => $faq['q'] ?? '',
            'answer'   => $faq['a'] ?? '',
        ])->all();

        if (empty($faqItems)) {
            $faqItems = [
                [
                    'question' => '¿Cada cuánto debo hacer mi ritual de piel?',
                    'answer' => 'Lo ideal es una rutina diaria simple (mañana y noche) y un ritual más completo 1 o 2 veces por semana. La constancia es lo que marca la diferencia.',
                ],
                [
                    'question' => '¿Los productos son aptos para todo tipo de piel?',
                    'answer' => 'Sí. En Belleza Áurea encuentras opciones para piel seca, mixta, grasa y sensible. Si tienes dudas, nuestro Quiz de piel te recomienda los productos ideales para ti.',
                ],
                [
                    'question' => '¿Hacen envíos a toda Colombia?',
                    'answer' => 'Sí, enviamos a todo el país. El costo y el tiempo de entrega dependen de tu ciudad; lo ves calculado en el carrito antes de pagar.',
                ],
            ];
        }

        $faqSchema = $this->seo->faqSchema($faqItems);

        $breadcrumbSchema = $this->seo->breadcrumbSchema([
            ['name' => 'Inicio', 'url' => url('/')],
            ['name' => 'Rituales', 'url' => $this->seo->localeUrl('blue-light')],
        ]);

        // Blog + blogPost[] con los rituales publicados.
        $blogSchema = $this->seo->blogSchema(
            BlogPost::published()->orderByDesc('published_at')->get(),
            $blueLightPage->hero_title ?: 'Rituales de belleza | Belleza Áurea',
            $this->seo->localeUrl('blue-light'),
        );

        $seoSettings = SeoSetting::getForPage('blue-light');

        return view('storefront.pages.que-es-luz-azul', compact('faqSchema', 'breadcrumbSchema', 'blogSchema', 'blueLightPage', 'seoSettings'));
    }

    public function contact(): View
    {
        $contactPage = ContactPageSetting::getCurrent();
        $seoSettings = SeoSetting::getForPage('contact');

        // Schema.org: ficha del negocio + ContactPage + miga de pan.
        $localBusinessSchema = $this->seo->localBusinessSchema($contactPage);
        $contactPageSchema = $this->seo->contactPageSchema();
        $breadcrumbSchema = $this->seo->breadcrumbSchema([
            ['name' => 'Inicio', 'url' => url('/')],
            ['name' => 'Contacto', 'url' => $this->seo->localeUrl('contact')],
        ]);

        return view('storefront.pages.contacto', compact(
            'contactPage', 'seoSettings',
            'localBusinessSchema', 'contactPageSchema', 'breadcrumbSchema',
        ));
    }

    public function shippingReturns(): View
    {
        $page = ShippingReturnsPageSetting::getCurrent();
        $seoSettings = SeoSetting::getForPage('shipping-returns');

        return view('storefront.pages.envios-y-devoluciones', compact('page', 'seoSettings'));
    }

    public function about(): View
    {
        $seoSettings = SeoSetting::getForPage('about');

        // Schema.org: AboutPage (mainEntity → Organization) + miga de pan.
        $aboutPageSchema = $this->seo->aboutPageSchema(
            $seoSettings->meta_description ?? null,
        );
        $breadcrumbSchema = $this->seo->breadcrumbSchema([
            ['name' => 'Inicio', 'url' => url('/')],
            ['name' => 'Sobre nosotras', 'url' => $this->seo->localeUrl('about')],
        ]);

        return view('storefront.pages.sobre-nosotras', compact(
            'seoSettings', 'aboutPageSchema', 'breadcrumbSchema',
        ));
    }

    public function terms(): View
    {
        $seoSettings = SeoSetting::getForPage('terms');

        return view('storefront.pages.terminos', compact('seoSettings'));
    }

    public function privacy(): View
    {
        $seoSettings = SeoSetting::getForPage('privacy');

        return view('storefront.pages.privacidad', compact('seoSettings'));
    }

    public function cookies(): View
    {
        $seoSettings = SeoSetting::getForPage('cookies');

        return view('storefront.pages.cookies', compact('seoSettings'));
    }
}
