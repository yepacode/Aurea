<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Product;
use App\Models\SeoSetting;
use App\Services\SeoService;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class BrandController extends Controller
{
    /**
     * /marcas — listado de todas las marcas activas. Cache 15 min.
     */
    public function index(SeoService $seoService): View
    {
        // La clave de cache ya incluye el locale, así que los schemas (URLs
        // absolutas por idioma) se pueden generar dentro del closure.
        $payload = Cache::remember('brands.index.v2.'.app()->getLocale(), 900, function () use ($seoService) {
            $brands = Brand::active()->ordered()
                ->whereHas('activeProducts')
                ->withCount('activeProducts')
                ->get();

            $s = SeoSetting::getForPage('brands');
            $seo = [
                'title'       => ($s->meta_title ?? null) ?: 'Marcas que distribuimos | Belleza Áurea',
                'description' => ($s->meta_description ?? null) ?: 'Descubre todas las marcas premium de cosmética, skincare y rituales que distribuimos en Belleza Áurea.',
                'canonical'   => ($s->canonical_url ?? null) ?: url('/marcas'),
                'og_image'    => ($s->og_image_url ?? null) ?: asset('img/brand/logo-principal.png'),
            ];

            // Schema.org: BreadcrumbList + ItemList de marcas
            $breadcrumbSchema = $seoService->breadcrumbSchema([
                ['name' => 'Inicio', 'url' => url('/')],
                ['name' => 'Marcas', 'url' => $seoService->localeUrl('brands.index')],
            ]);
            $brandsListSchema = $seoService->brandListSchema($brands, 'Marcas que distribuimos');

            return compact('brands', 'seo', 'breadcrumbSchema', 'brandsListSchema');
        });

        return view('storefront.brands.index', $payload);
    }

    /**
     * /marcas/{slug} — página de una marca con sus productos.
     */
    public function show(SeoService $seoService, string $slug): View
    {
        $brand = Brand::active()->where('slug', $slug)->firstOrFail();

        $products = Product::active()->where('brand_id', $brand->id)
            ->with('category')
            ->orderBy('sort_order')
            ->paginate(24);

        // Productos destacados (para landing enriquecida)
        $featuredProducts = collect();
        $featuredIds = is_array($brand->featured_products_json) ? $brand->featured_products_json : [];
        if ($brand->landing_enabled && ! empty($featuredIds)) {
            $featuredProducts = Product::active()
                ->where('brand_id', $brand->id)
                ->whereIn('id', $featuredIds)
                ->with('category')
                ->get()
                ->sortBy(function ($p) use ($featuredIds) {
                    return array_search($p->id, $featuredIds);
                })
                ->values();
        }

        $image = $brand->logo_url ?: asset('img/brand/logo-principal.png');
        $meta = $seoService->meta(
            $brand->meta_title ?: ($brand->name.' | Belleza Áurea'),
            $brand->meta_description
                ?: ($brand->short_description ?: 'Conoce todos los productos de '.$brand->name.' disponibles en Belleza Áurea.'),
            null,
            route('brands.show', $brand->slug),
            'website',
        );
        $meta['og_image'] = $image;
        $meta['twitter_image'] = $image;
        $seo = $seoService->applyItemSeo($meta, $brand, 'og_image_path', 'twitter_image_path');

        // Schema.org Brand — chr(64) evita que Blade procese @context/@type como directivas
        $K_CTX = chr(64).'context';
        $K_TYP = chr(64).'type';
        $brandSchema = json_encode([
            $K_CTX => 'https://schema.org',
            $K_TYP => 'Brand',
            'name' => $brand->name,
            'url'  => route('brands.show', $brand->slug),
            'logo' => $brand->logo_url,
            'description' => $brand->short_description,
            'sameAs' => array_values(array_filter([$brand->website_url])),
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        // BreadcrumbList: Inicio › Marcas › {Marca}
        $breadcrumbSchema = $seoService->breadcrumbSchema([
            ['name' => 'Inicio', 'url' => url('/')],
            ['name' => 'Marcas', 'url' => $seoService->localeUrl('brands.index')],
            ['name' => $brand->name, 'url' => $seoService->localeUrl('brands.show', ['slug' => $brand->slug])],
        ]);

        // ItemList con los productos de la marca (los de la página actual).
        $productsListSchema = $seoService->productListSchema(
            $products->getCollection(),
            'Productos de '.$brand->name,
        );

        $view = $brand->landing_enabled
            ? 'storefront.brands.show-landing'
            : 'storefront.brands.show';

        return view($view, compact(
            'brand', 'products', 'featuredProducts', 'seo', 'brandSchema',
            'breadcrumbSchema', 'productsListSchema',
        ));
    }
}
