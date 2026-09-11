<?php

namespace App\Http\Controllers;

use App\Models\BlogPageSetting;
use App\Models\BlogPost;
use App\Models\Product;
use App\Services\SeoService;
use Illuminate\View\View;

class BlogController extends Controller
{
    public function __construct(
        private SeoService $seo,
    ) {}

    public function index(): View
    {
        $posts = BlogPost::published()
            ->orderByDesc('published_at')
            ->paginate(9);

        // Asigna una categoría a cada post a partir de su focus_keyword.
        // Las claves coinciden con los data-filter de los botones.
        $categoryLabels = [
            'skincare'   => 'Skincare',
            'unas'       => 'Uñas',
            'maquillaje' => 'Maquillaje',
            'cabello'    => 'Cabello',
            'rituales'   => 'Rituales',
        ];

        $validKeys = array_keys($categoryLabels);
        $categorize = function (?string $keyword) use ($validKeys): string {
            $t = mb_strtolower($keyword ?? '');
            if (str_contains($t, 'esmalte') || str_contains($t, 'uña') || str_contains($t, 'unas') || str_contains($t, 'manicure')) return 'unas';
            if (str_contains($t, 'labial') || str_contains($t, 'sombra') || str_contains($t, 'rubor') || str_contains($t, 'maquillaje')) return 'maquillaje';
            if (str_contains($t, 'cabello') || str_contains($t, 'pelo') || str_contains($t, 'shampoo') || str_contains($t, 'peluqueria')) return 'cabello';
            if (str_contains($t, 'ritual') || str_contains($t, 'rutina')) return 'rituales';
            return 'skincare'; // fallback razonable para belleza
        };

        // Si el admin eligió categoría manual la usamos; si no, la inferimos del keyword.
        $posts->getCollection()->transform(function (BlogPost $post) use ($categorize, $validKeys) {
            $manual = $post->category ?? null;
            $post->category_key = ($manual && in_array($manual, $validKeys, true))
                ? $manual
                : $categorize($post->focus_keyword);
            return $post;
        });

        // Solo mostramos los botones de las categorías que tienen al menos un post visible.
        $countsByCategory = $posts->getCollection()
            ->groupBy('category_key')
            ->map->count();

        $availableCategories = collect($categoryLabels)
            ->filter(fn ($label, $key) => isset($countsByCategory[$key]))
            ->map(fn ($label, $key) => [
                'key' => $key,
                'label' => $label,
                'count' => $countsByCategory[$key] ?? 0,
            ])
            ->values();

        $breadcrumbs = $this->seo->breadcrumbSchema([
            ['name' => 'Inicio', 'url' => url('/')],
            ['name' => 'Blog', 'url' => route('blog.index')],
        ]);

        $blogPage = BlogPageSetting::getCurrent();

        return view('storefront.blog.index', [
            'posts' => $posts,
            'availableCategories' => $availableCategories,
            'totalPostsOnPage' => $posts->count(),
            'breadcrumbs' => $breadcrumbs,
            'blogPage' => $blogPage,
        ]);
    }

    public function show(string $slug): View
    {
        $post = BlogPost::published()
            ->where('slug', $slug)
            ->firstOrFail();

        $seo = $this->seo->forBlogPost($post);
        $schema = $this->seo->articleSchema($post);

        // Si se entra por /rituales/{slug}, la miga de pan es "Rituales"; si no, "Blog".
        $isRitual = request()->routeIs('ritual.show');
        $parentCrumb = $isRitual
            ? ['name' => 'Rituales', 'url' => route('blue-light')]
            : ['name' => 'Blog', 'url' => route('blog.index')];
        $selfUrl = $isRitual ? route('ritual.show', $post->slug) : route('blog.show', $post->slug);

        $breadcrumbs = $this->seo->breadcrumbSchema([
            ['name' => 'Inicio', 'url' => url('/')],
            $parentCrumb,
            ['name' => $post->title, 'url' => $selfUrl],
        ]);
        // Para la miga de pan visible en la vista
        $crumbParentLabel = $parentCrumb['name'];
        $crumbParentUrl = $parentCrumb['url'];

        $recent = BlogPost::published()
            ->where('id', '!=', $post->id)
            ->orderByDesc('published_at')
            ->limit(3)
            ->get();

        // Productos reales de lentes (se excluyen toallitas), aleatorios y solo con stock.
        $typeLabels = [
            'miopia' => 'Miopía',
            'lectura' => 'Lectura',
            'sin_graduacion' => 'Sin Graduación',
            'toallitas' => 'Toallitas',
        ];

        $products = Product::active()
            ->with('variants')
            ->where(function ($q) {
                $q->whereJsonContains('type', 'miopia')
                  ->orWhereJsonContains('type', 'lectura')
                  ->orWhereJsonContains('type', 'sin_graduacion');
            })
            ->where(function ($q) {
                $q->where('stock', '>', 0)
                  ->orWhereHas('variants', fn ($v) => $v->where('is_active', true)->where('stock', '>', 0));
            })
            ->inRandomOrder()
            ->limit(3)
            ->get()
            ->filter(fn ($p) => $p->hasStock())
            ->map(function (Product $p) use ($typeLabels) {
                $primaryType = ($p->type[0] ?? null);
                $hasCompare = $p->compare_price && (float) $p->compare_price > (float) $p->price;
                return [
                    'name' => $p->name,
                    'slug' => $p->slug,
                    'price' => '$' . number_format((float) $p->price, 2),
                    'original_price' => $hasCompare ? '$' . number_format((float) $p->compare_price, 2) : null,
                    'type' => $typeLabels[$primaryType] ?? 'Producto',
                    'image' => $p->images[0] ?? null,
                ];
            })
            ->values();

        return view('storefront.blog.show', compact(
            'post', 'seo', 'schema', 'breadcrumbs', 'recent', 'products',
            'crumbParentLabel', 'crumbParentUrl',
        ));
    }
}
