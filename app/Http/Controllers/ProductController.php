<?php

namespace App\Http\Controllers;

use App\Helpers\ColorHelper;
use App\Models\Category;
use App\Models\LentesPageSetting;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\SeoSetting;
use App\Services\SeoService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function __construct(
        private SeoService $seo,
    ) {}

    public function index(Request $request): View
    {
        // ── Filtros de belleza ──
        $qFiltro      = trim((string) $request->input('q', ''));    // búsqueda libre
        $catFiltro    = $request->input('category', '');   // slug categoría
        $brandFiltro  = $request->input('brand', '');      // slug marca
        $priceFiltro  = $request->input('price', '');      // ej: '0-10000', '10000-25000', etc.
        $sortFiltro   = $request->input('sort', 'relevant'); // relevant|price-asc|price-desc|new|az

        $query = Product::active()->with(['variants', 'category', 'brand'])
            ->where(function ($q) {
                $q->where('stock', '>', 0)
                  ->orWhereHas('variants', fn ($v) => $v->where('is_active', true)->where('stock', '>', 0));
            });

        if ($qFiltro !== '') {
            $like = '%'.str_replace(['%', '_'], ['\\%', '\\_'], $qFiltro).'%';
            $query->where(function ($w) use ($like) {
                $w->where('name', 'like', $like)
                  ->orWhere('description', 'like', $like)
                  ->orWhere('internal_code', 'like', $like)
                  ->orWhereHas('brand', fn ($b) => $b->where('name', 'like', $like))
                  ->orWhereHas('category', fn ($c) => $c->where('name', 'like', $like));
            });
        }

        if ($catFiltro) {
            $query->whereHas('category', fn ($q) => $q->where('slug', $catFiltro));
        }

        if ($brandFiltro) {
            $query->whereHas('brand', fn ($q) => $q->where('slug', $brandFiltro));
        }

        if ($priceFiltro && preg_match('/^(\d+)-(\d+|max)$/', $priceFiltro, $m)) {
            $query->where('price', '>=', (int) $m[1]);
            if ($m[2] !== 'max') {
                $query->where('price', '<=', (int) $m[2]);
            }
        }

        // Solo productos con stock (en el producto o en alguna variante activa).
        // Se filtra en SQL para poder paginar sin cargar todo el catálogo.
        $query->where(function ($q) {
            $q->where('stock', '>', 0)
              ->orWhereHas('variants', fn ($v) => $v->where('is_active', true)->where('stock', '>', 0));
        });

        // ── Ordenamiento ──
        // Base: con imagen primero siempre (sin foto al final)
        $query->orderByRaw('CASE WHEN images IS NOT NULL AND JSON_LENGTH(images) > 0 THEN 0 ELSE 1 END');

        match ($sortFiltro) {
            'price-asc'  => $query->orderBy('price', 'asc'),
            'price-desc' => $query->orderBy('price', 'desc'),
            'new'        => $query->orderByDesc('created_at'),
            'az'         => $query->orderBy('name', 'asc'),
            default      => $query->orderByDesc('is_featured')->orderBy('sort_order')->orderByDesc('created_at'),
        };

        $products = $query->paginate(24)->withQueryString();

        // ── Datos para los filtros ──
        // Categorías con conteo de productos activos con stock
        $categoriasFiltro = Category::orderBy('sort_order')->orderBy('name')->get()
            ->map(function ($c) {
                $count = Product::active()
                    ->where('category_id', $c->id)
                    ->where(function ($q) {
                        $q->where('stock', '>', 0)
                          ->orWhereHas('variants', fn ($v) => $v->where('is_active', true)->where('stock', '>', 0));
                    })->count();
                $c->products_count = $count;
                return $c;
            })
            ->filter(fn ($c) => $c->products_count > 0)
            ->values();

        // Marcas con conteo (solo si hay productos con marca)
        $marcasFiltro = \App\Models\Brand::orderBy('name')->get()
            ->map(function ($b) {
                $count = Product::active()
                    ->where('brand_id', $b->id)
                    ->where(function ($q) {
                        $q->where('stock', '>', 0)
                          ->orWhereHas('variants', fn ($v) => $v->where('is_active', true)->where('stock', '>', 0));
                    })->count();
                $b->products_count = $count;
                return $b;
            })
            ->filter(fn ($b) => $b->products_count > 0)
            ->values();

        // Rangos de precio (configurados pensando en el catálogo de belleza CO)
        $rangosPrecios = [
            '0-10000'        => 'Hasta $10.000',
            '10000-25000'    => '$10.000 – $25.000',
            '25000-50000'    => '$25.000 – $50.000',
            '50000-100000'   => '$50.000 – $100.000',
            '100000-max'     => 'Más de $100.000',
        ];

        $opcionesOrden = [
            'relevant'   => 'Más relevantes',
            'price-asc'  => 'Precio: menor a mayor',
            'price-desc' => 'Precio: mayor a menor',
            'new'        => 'Más recientes',
            'az'         => 'Nombre A → Z',
        ];

        $breadcrumbs = $this->seo->breadcrumbSchema([
            ['name' => 'Inicio',    'url' => url('/')],
            ['name' => 'Productos', 'url' => route('products.index')],
        ]);

        // Ajustes editables de la página (H1/subtítulo) y SEO (meta) desde el admin.
        $lentesPage  = LentesPageSetting::getCurrent();
        $seoSettings = SeoSetting::getForPage('products-index');

        // Si se está filtrando por UNA categoría, sus overrides SEO mandan.
        $categorySeo = null;
        if ($catFiltro) {
            $cat = Category::where('slug', $catFiltro)->first();
            if ($cat) {
                $base = $this->seo->meta(
                    $cat->meta_title ?: ($cat->name.' | Belleza Áurea'),
                    $cat->meta_description ?: ('Explora la categoría '.$cat->name.' en Belleza Áurea. Cosmética e insumos de belleza con envío a toda Colombia.'),
                    $cat->image ? asset('storage/'.$cat->image) : null,
                    url()->current(),
                    'website',
                );
                $categorySeo = $this->seo->applyItemSeo($base, $cat, 'og_image_path', 'twitter_image_path');
            }
        }

        $wishlistIds = \Illuminate\Support\Facades\Auth::guard('customer')->check()
            ? \Illuminate\Support\Facades\Auth::guard('customer')->user()->wishlist()->pluck('products.id')
            : collect();

        return view('storefront.products.index', [
            'categorySeo'      => $categorySeo,
            'bestSellerIds'    => Product::bestSellerIds(8),
            'wishlistIds'      => $wishlistIds,
            'products'         => $products,
            'categoriasFiltro' => $categoriasFiltro,
            'marcasFiltro'     => $marcasFiltro,
            'rangosPrecios'    => $rangosPrecios,
            'opcionesOrden'    => $opcionesOrden,
            'qFiltro'          => $qFiltro,
            'catFiltro'        => $catFiltro,
            'brandFiltro'      => $brandFiltro,
            'priceFiltro'      => $priceFiltro,
            'sortFiltro'       => $sortFiltro,
            'breadcrumbs'      => $breadcrumbs,
            'lentesPage'       => $lentesPage,
            'seoSettings'      => $seoSettings,
        ]);
    }

    public function show(string $slug): View
    {
        $product = Product::active()
            ->where('slug', $slug)
            ->with(['variants', 'category', 'brand', 'approvedReviews'])
            ->firstOrFail();

        $activeVariants = $product->variants->where('is_active', true);

        // Unique colors (variantes con color_hex o option_type=color)
        $colores = $activeVariants
            ->filter(fn ($v) => $v->option_type === 'color' || ! empty($v->color_hex))
            ->pluck('color')->unique()->filter()->values();

        // Variantes genéricas (no color, no graduación) agrupadas por etiqueta visible.
        // Estructura:
        //   ['Tamaño' => collection<variant>, 'Acabado' => collection<variant>, ...]
        $genericVariants = $activeVariants
            ->filter(fn ($v) => $v->option_type !== 'color' && empty($v->graduation_type))
            ->groupBy(fn ($v) => $v->name ?: \App\Models\ProductVariant::DEFAULT_LABELS[$v->option_type] ?? 'Opción');

        // Productos relacionados: misma categoría, otros productos con stock e imagen.
        $relatedProducts = collect();
        if ($product->category_id) {
            $relatedProducts = Product::active()
                ->where('category_id', $product->category_id)
                ->where('id', '!=', $product->id)
                ->where(function ($q) {
                    $q->where('stock', '>', 0)
                      ->orWhereHas('variants', fn ($v) => $v->where('is_active', true)->where('stock', '>', 0));
                })
                ->whereNotNull('images')
                ->whereRaw('JSON_LENGTH(images) > 0')
                ->with(['brand'])
                ->inRandomOrder()
                ->take(4)
                ->get();
        }

        $isBestSeller = Product::bestSellerIds(8)->contains($product->id);
        $inWishlist = \Illuminate\Support\Facades\Auth::guard('customer')->check()
            && \Illuminate\Support\Facades\Auth::guard('customer')->user()->hasInWishlist($product->id);

        $seo = $this->seo->forProduct($product);
        $schema = $this->seo->productSchema($product);
        $howToSchema = $this->seo->howToSchema($product);
        $breadcrumbs = $this->seo->breadcrumbSchema([
            ['name' => 'Inicio',    'url' => url('/')],
            ['name' => 'Productos', 'url' => route('products.index')],
            ['name' => $product->name, 'url' => route('products.show', $product->slug)],
        ]);

        return view('storefront.products.show', compact(
            'product', 'colores', 'genericVariants',
            'relatedProducts', 'isBestSeller', 'inWishlist', 'seo', 'schema', 'howToSchema', 'breadcrumbs',
        ));
    }
}
