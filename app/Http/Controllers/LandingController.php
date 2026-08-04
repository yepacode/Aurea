<?php

namespace App\Http\Controllers;

use App\Mail\LeadWelcome;
use App\Models\Lead;
use App\Models\Product;
use App\Models\QuizPageSetting;
use App\Models\SeoSetting;
use App\Services\SeoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class LandingController extends Controller
{
    public function __construct(
        private SeoService $seo,
    ) {}

    /**
     * Quiz de piel — recomendación de productos.
     */
    public function quiz(): View
    {
        $quizPage = QuizPageSetting::getCurrent();
        $questions = $quizPage->getQuestionsOrDefault();
        $seoSettings = SeoSetting::getForPage('quiz');

        return view('storefront.landing.quiz', compact('quizPage', 'questions', 'seoSettings'));
    }

    /**
     * Store quiz result as lead (AJAX).
     */
    public function quizResult(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'answers' => 'required|array',
        ]);

        $lead = Lead::updateOrCreate(
            ['email' => $validated['email']],
            [
                'name' => $validated['name'],
                'source' => 'quiz',
            ],
        );

        if ($lead->wasRecentlyCreated) {
            Mail::to($lead->email)->send(new LeadWelcome($lead));
        }

        // Determine recommendations based on answers
        $recommendation = $this->getRecommendations($validated['answers']);

        return response()->json([
            'success' => true,
            'recommendation' => $recommendation,
        ]);
    }

    /**
     * Determine a SET of product recommendations from quiz answers.
     * Prioritises products of the category that matches the user's "interest",
     * then fills up with featured / recent products. Returns up to 4.
     */
    private function getRecommendations(array $answers): array
    {
        $interest = $answers['interest'] ?? null;

        // Keywords per interest → se buscan en el nombre de la categoría o del producto.
        $keywords = [
            'unas'       => ['uña', 'esmalte', 'nail', 'decora', 'pincel', 'preparad', 'gancho'],
            'skincare'   => ['piel', 'skin', 'facial', 'crema', 'sérum', 'serum', 'jabon', 'mantequilla'],
            'maquillaje' => ['maquillaje', 'labial', 'base', 'sombra', 'rubor', 'makeup', 'polvo'],
            'cabello'    => ['cabello', 'peluquer', 'hair', 'capilar', 'shampoo'],
        ];

        $products = collect();

        if ($interest && isset($keywords[$interest])) {
            $kw = $keywords[$interest];
            $products = Product::active()->with(['category', 'variants'])
                ->where(function ($q) use ($kw) {
                    foreach ($kw as $k) {
                        $q->orWhere('name', 'like', "%{$k}%")
                          ->orWhereHas('category', fn ($c) => $c->where('name', 'like', "%{$k}%"));
                    }
                })
                ->get()
                ->filter(fn ($p) => $p->hasStock())
                ->values();
        }

        // Rellenar hasta 4 con destacados y, si faltan, con los más recientes.
        if ($products->count() < 4) {
            $fill = Product::active()->featured()->with(['category', 'variants'])->get()
                ->filter(fn ($p) => $p->hasStock());

            if ($fill->count() < 4) {
                $fill = $fill->concat(
                    Product::active()->with(['category', 'variants'])->latest()->get()
                        ->filter(fn ($p) => $p->hasStock())
                );
            }

            $ids = $products->pluck('id')->all();
            foreach ($fill as $p) {
                if ($products->count() >= 4) break;
                if (! in_array($p->id, $ids, true)) {
                    $products->push($p);
                    $ids[] = $p->id;
                }
            }
        }

        // Mensaje personalizado según tipo de piel + preocupación.
        $skin = [
            'grasa' => 'grasa', 'seca' => 'seca', 'mixta' => 'mixta', 'sensible' => 'sensible',
        ][$answers['skin_type'] ?? ''] ?? null;
        $concern = [
            'brillo' => 'controlar el brillo', 'resequedad' => 'hidratar y calmar',
            'manchas' => 'unificar el tono', 'lineas' => 'nutrir y prevenir',
        ][$answers['concern'] ?? ''] ?? null;

        $message = 'Según tus respuestas, esto te puede servir:';
        if ($skin && $concern) {
            $message = "Para tu piel {$skin}, enfocada en {$concern}, esto te puede servir:";
        } elseif ($skin) {
            $message = "Para tu piel {$skin}, esto te puede servir:";
        }

        return [
            'message' => $message,
            'products' => $products->take(4)->map(fn ($p) => [
                'name'     => $p->name,
                'price'    => $p->price,
                'image'    => $p->images[0] ?? null,
                'url'      => route('products.show', $p->slug),
                'category' => $p->category?->name,
            ])->values()->all(),
        ];
    }
}
