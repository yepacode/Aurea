<?php

namespace App\Http\Controllers;

use App\Models\ContactPageSetting;
use App\Models\Faq;
use App\Models\FaqCategory;
use App\Models\SeoSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class FaqController extends Controller
{
    /**
     * Página pública SEO con todas las FAQs y schema.org. Cache 15 min.
     */
    public function index(): View
    {
        $payload = Cache::remember('faq.index.v1.'.app()->getLocale(), 900, function () {
            $categories = FaqCategory::active()
                ->with(['faqs' => function ($q) {
                    $q->active()->orderBy('sort_order')->orderBy('id');
                }])
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get();

            return [
                'categories'  => $categories,
                'whatsappUrl' => ContactPageSetting::whatsappUrl(),
                'seoSettings' => SeoSetting::getForPage('faq'),
            ];
        });

        return view('storefront.faq.index', $payload);
    }

    /**
     * JSON — lista de categorías activas con conteo de FAQs. Cache 15 min.
     */
    public function categoriesJson(): JsonResponse
    {
        $categories = Cache::remember('faq.categories.v1.'.app()->getLocale(), 900, function () {
            return FaqCategory::active()
                ->withCount(['faqs' => fn ($q) => $q->where('is_active', true)])
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get()
                ->map(fn (FaqCategory $c) => [
                    'id'    => $c->id,
                    'name'  => $c->name,
                    'slug'  => $c->slug,
                    'emoji' => $c->emoji,
                    'count' => (int) $c->faqs_count,
                ]);
        });

        return response()->json(['data' => $categories]);
    }

    /**
     * JSON — FAQs de una categoría por slug. Cache 15 min por slug.
     */
    public function categoryJson(string $slug): JsonResponse
    {
        $payload = Cache::remember(
            'faq.category.'.$slug.'.v1.'.app()->getLocale(),
            900,
            function () use ($slug) {
                $category = FaqCategory::active()->where('slug', $slug)->firstOrFail();

                $faqs = $category->faqs()
                    ->active()
                    ->orderBy('sort_order')
                    ->orderBy('id')
                    ->get(['id', 'question', 'answer'])
                    ->map(fn (Faq $f) => [
                        'id'       => $f->id,
                        'question' => $f->question,
                        'answer'   => $f->answer,
                    ]);

                return [
                    'category' => [
                        'id'    => $category->id,
                        'name'  => $category->name,
                        'slug'  => $category->slug,
                        'emoji' => $category->emoji,
                    ],
                    'faqs' => $faqs,
                ];
            }
        );

        return response()->json($payload);
    }

    /**
     * JSON — buscador. LIKE %q% en question y answer, máx 10.
     */
    public function search(Request $request): JsonResponse
    {
        $q = trim((string) $request->query('q', ''));

        if (mb_strlen($q) < 2) {
            return response()->json(['data' => []]);
        }

        $like = '%' . str_replace(['%', '_'], ['\\%', '\\_'], $q) . '%';

        $faqs = Faq::active()
            ->whereHas('category', fn ($c) => $c->where('is_active', true))
            ->where(function ($w) use ($like) {
                $w->where('question', 'like', $like)
                  ->orWhere('answer', 'like', $like);
            })
            ->with('category:id,name,slug,emoji')
            ->orderByDesc('helpful_count')
            ->orderByDesc('view_count')
            ->limit(10)
            ->get(['id', 'faq_category_id', 'question', 'answer'])
            ->map(fn (Faq $f) => [
                'id'       => $f->id,
                'question' => $f->question,
                'answer'   => $f->answer,
                'category' => [
                    'name'  => $f->category?->name,
                    'slug'  => $f->category?->slug,
                    'emoji' => $f->category?->emoji,
                ],
            ]);

        return response()->json(['data' => $faqs]);
    }

    /**
     * Marca "útil" (incrementa contador).
     */
    public function helpful(int $id): JsonResponse
    {
        $faq = Faq::active()->findOrFail($id);
        $faq->increment('helpful_count');

        return response()->json([
            'ok'             => true,
            'helpful_count'  => $faq->helpful_count,
        ]);
    }

    /**
     * Registra que la FAQ fue vista.
     */
    public function view(int $id): JsonResponse
    {
        $faq = Faq::active()->findOrFail($id);
        $faq->increment('view_count');

        return response()->json([
            'ok'          => true,
            'view_count'  => $faq->view_count,
        ]);
    }
}
