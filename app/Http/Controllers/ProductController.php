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
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
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
        // Conteos agregados (categorías + marcas) en 2 queries en vez de N+N.
        // Se cachean 5 min: los conteos del sidebar no necesitan ser al segundo.
        [$categoryCounts, $brandCounts] = Cache::remember('catalog.sidebar-counts', 300, function () {
            $stockCondition = function ($q) {
                $q->where('stock', '>', 0)
                  ->orWhereHas('variants', fn ($v) => $v->where('is_active', true)->where('stock', '>', 0));
            };

            $categoryCounts = Product::query()
                ->active()
                ->select('category_id', DB::raw('COUNT(*) as cnt'))
                ->whereNotNull('category_id')
                ->where($stockCondition)
                ->groupBy('category_id')
                ->pluck('cnt', 'category_id')
                ->toArray();

            $brandCounts = Product::query()
                ->active()
                ->select('brand_id', DB::raw('COUNT(*) as cnt'))
                ->whereNotNull('brand_id')
                ->where($stockCondition)
                ->groupBy('brand_id')
                ->pluck('cnt', 'brand_id')
                ->toArray();

            return [$categoryCounts, $brandCounts];
        });

        // Categorías con conteo de productos activos con stock
        $categoriasFiltro = Category::orderBy('sort_order')->orderBy('name')->get()
            ->map(function ($c) use ($categoryCounts) {
                $c->products_count = (int) ($categoryCounts[$c->id] ?? 0);
                return $c;
            })
            ->filter(fn ($c) => $c->products_count > 0)
            ->values();

        // Marcas con conteo (solo si hay productos con marca)
        $marcasFiltro = \App\Models\Brand::orderBy('name')->get()
            ->map(function ($b) use ($brandCounts) {
                $b->products_count = (int) ($brandCounts[$b->id] ?? 0);
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

        // Productos relacionados: prioridad en cascada
        //   1) MISMA categoría, más vendidos primero (según pedidos pagados);
        //      empatan por updated_at desc para tener siempre 4 candidatos.
        //   2) Si aún no llega a 4: MISMA marca.
        //   3) Si aún no llega: cualquier producto activo (random).
        // Todo excluyendo el producto actual y exigiendo stock + imagen.
        $target = 4;
        $relatedProducts = collect();

        $baseQuery = function () use ($product) {
            return Product::active()
                ->where('id', '!=', $product->id)
                ->where(function ($q) {
                    $q->where('stock', '>', 0)
                      ->orWhereHas('variants', fn ($v) => $v->where('is_active', true)->where('stock', '>', 0));
                })
                ->whereNotNull('images')
                ->whereRaw('JSON_LENGTH(images) > 0')
                ->with(['brand']);
        };

        // 1) Misma categoría, ordenado por ventas (LEFT JOIN a un subquery agregado).
        if ($product->category_id) {
            $relatedProducts = $baseQuery()
                ->where('category_id', $product->category_id)
                ->leftJoinSub(
                    \App\Models\OrderItem::query()
                        ->join('orders', 'orders.id', '=', 'order_items.order_id')
                        ->where('orders.payment_status', 'paid')
                        ->selectRaw('product_id, SUM(qty) as sold')
                        ->groupBy('product_id'),
                    'sales',
                    'sales.product_id',
                    '=',
                    'products.id',
                )
                ->orderByRaw('COALESCE(sales.sold, 0) DESC')
                ->orderByDesc('products.updated_at')
                ->select('products.*')
                ->take($target)
                ->get();
        }

        // 2) Rellenar con MISMA marca si aún faltan.
        if ($relatedProducts->count() < $target && $product->brand_id) {
            $need = $target - $relatedProducts->count();
            $excludeIds = $relatedProducts->pluck('id')->push($product->id)->all();
            $brandFill = $baseQuery()
                ->where('brand_id', $product->brand_id)
                ->whereNotIn('products.id', $excludeIds)
                ->orderByDesc('products.updated_at')
                ->take($need)
                ->get();
            $relatedProducts = $relatedProducts->concat($brandFill);
        }

        // 3) Rellenar con productos activos aleatorios si aún faltan.
        if ($relatedProducts->count() < $target) {
            $need = $target - $relatedProducts->count();
            $excludeIds = $relatedProducts->pluck('id')->push($product->id)->all();
            $randomFill = $baseQuery()
                ->whereNotIn('products.id', $excludeIds)
                ->inRandomOrder()
                ->take($need)
                ->get();
            $relatedProducts = $relatedProducts->concat($randomFill);
        }

        $relatedProducts = $relatedProducts->take($target)->values();

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
