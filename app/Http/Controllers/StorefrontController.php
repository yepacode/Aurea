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
use Illuminate\View\View;

class StorefrontController extends Controller
{
    public function __construct(
        private SeoService $seo,
    ) {}

    public function home(): View
    {
        // Productos principales del home: featured primero, luego sort_order,
        // luego más recientes. Garantiza stock. Cargamos variantes + categoría
        // + brand para las cards.
        // Filtro de imagen: solo productos con al menos 1 imagen (campo images
        // es JSON array). En el home solo se ven productos "presentables".
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

        // Producto estrella:
        //   1. Si el admin fijó star_product_id Y ese producto cumple los criterios
        //      (imagen + descripción + precio > 0), se respeta.
        //   2. En cualquier otro caso → rota aleatoriamente entre productos activos
        //      con stock, imagen, descripción y precio. Cada visita carga uno distinto.
        $heroProduct = null;
        if ($homePage->star_product_id) {
            $candidate = Product::active()
                ->where('id', $homePage->star_product_id)
                ->whereNotNull('images')
                ->whereRaw('JSON_LENGTH(images) > 0')
                ->whereNotNull('description')
                ->where('description', '!=', '')
                ->where('price', '>', 0)
                ->with('variants')
                ->first();
            if ($candidate) {
                $heroProduct = $candidate;
            }
        }
        if (! $heroProduct) {
            $heroProduct = Product::active()
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

        // Más vendidos — hasta 8 productos activos con stock, ordenados por
        // Product::bestSellerIds (unidades vendidas en pedidos pagados).
        $bestSellerIds = Product::bestSellerIds(8);
        $bestSellers = collect();
        if ($bestSellerIds->isNotEmpty()) {
            $bestSellers = Product::active()
                ->whereIn('id', $bestSellerIds)
                ->where(function ($q) {
                    $q->where('stock', '>', 0)
                      ->orWhereHas('variants', fn ($v) => $v->where('is_active', true)->where('stock', '>', 0));
                })
                ->whereNotNull('images')
                ->whereRaw('JSON_LENGTH(images) > 0')
                ->with(['variants', 'category', 'brand'])
                ->get()
                ->filter(fn ($p) => $p->hasStock())
                ->sortBy(fn ($p) => $bestSellerIds->search($p->id))
                ->values()
                ->take(8);
        }

        return view('storefront.home', compact(
            'hero', 'heroProduct', 'heroMode', 'homePage', 'seoSettings',
            'lentes', 'toallitas', 'coloresDisponibles',
            'recentPosts', 'infographics', 'organizationSchema', 'faqSchema',
            'categories', 'testimonials', 'featuredBrands', 'featuredBundles',
            'bestSellers',
        ));
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
            ['name' => 'Rituales', 'url' => route('blue-light')],
        ]);

        $seoSettings = SeoSetting::getForPage('blue-light');

        return view('storefront.pages.que-es-luz-azul', compact('faqSchema', 'breadcrumbSchema', 'blueLightPage', 'seoSettings'));
    }

    public function contact(): View
    {
        $contactPage = ContactPageSetting::getCurrent();
        $seoSettings = SeoSetting::getForPage('contact');

        return view('storefront.pages.contacto', compact('contactPage', 'seoSettings'));
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

        return view('storefront.pages.sobre-nosotras', compact('seoSettings'));
    }

    public function terms(): View
    {
        return view('storefront.pages.terminos');
    }

    public function privacy(): View
    {
        return view('storefront.pages.privacidad');
    }

    public function cookies(): View
    {
        return view('storefront.pages.cookies');
    }
}
