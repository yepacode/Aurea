<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SeoSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminSeoController extends Controller
{
    /**
     * Lista de páginas del storefront con SEO editable.
     * 'legacy' => true marca páginas heredadas de la etapa NUVION (blue-light, quiz)
     * que ya no están enlazadas desde el nav/footer pero siguen accesibles por URL.
     */
    private array $pageLabels = [
        // Núcleo del storefront
        'home'              => ['label' => 'Inicio',                'desc' => 'Página principal (/)'],
        'products-index'    => ['label' => 'Catálogo',              'desc' => 'Listado de productos (/productos)'],
        'bundles-index'     => ['label' => 'Kits',                  'desc' => 'Página de kits y combos (/kits)'],
        'subscriptions-index' => ['label' => 'Rituales mensuales',  'desc' => 'Planes de suscripción (/rituales-mensuales)'],
        'brands'            => ['label' => 'Marcas',                'desc' => 'Listado de marcas (/marcas)'],
        'blog'              => ['label' => 'Blog',                  'desc' => 'Blog de tips y rituales (/blog)'],
        // Contenido y ayuda
        'about'             => ['label' => 'Sobre nosotras',        'desc' => 'Historia y valores (/sobre-nosotras)'],
        'contact'           => ['label' => 'Contacto',              'desc' => 'Formulario de contacto (/contacto)'],
        'shipping-returns'  => ['label' => 'Envíos y devoluciones', 'desc' => 'Política de envíos (/envios-y-devoluciones)'],
        'faq'               => ['label' => 'Preguntas frecuentes',  'desc' => 'FAQs (/preguntas-frecuentes)'],
        // Legales
        'terms'             => ['label' => 'Términos y condiciones','desc' => 'Legal (/terminos-y-condiciones)'],
        'privacy'           => ['label' => 'Política de privacidad','desc' => 'Legal (/politica-de-privacidad)'],
        'cookies'           => ['label' => 'Política de cookies',   'desc' => 'Legal (/politica-de-cookies)'],
        // Programas de cliente (dentro de "Mi cuenta")
        'wholesale'         => ['label' => 'Mayoristas',            'desc' => 'Solicitud de cuenta mayorista (/cuenta/mayorista/solicitar)'],
        'referrals'         => ['label' => 'Programa de referidos', 'desc' => 'Programa de referidos (/cuenta/referidos)'],
        // Páginas heredadas — se mantienen accesibles por URL pero ya no van en el nav
        'blue-light'        => ['label' => 'Rituales (legacy)',     'desc' => 'Página heredada NUVION (/rituales) — ya no está en el menú', 'legacy' => true],
        'quiz'              => ['label' => 'Quiz de piel (legacy)', 'desc' => 'Landing heredada NUVION (/landing) — ya no está en el menú', 'legacy' => true],
    ];

    public function index()
    {
        $pages = [];
        foreach ($this->pageLabels as $key => $meta) {
            $seo = SeoSetting::getForPage($key);
            $pages[] = [
                'key'         => $key,
                'label'       => $meta['label'],
                'desc'        => $meta['desc'] ?? null,
                'legacy'      => $meta['legacy'] ?? false,
                'configured'  => $seo && $seo->meta_title,
                'meta_title'  => $seo->meta_title ?? null,
            ];
        }

        return view('admin.seo.index', compact('pages'));
    }

    public function edit(string $pageKey)
    {
        $seo = SeoSetting::getForPage($pageKey)
            ?? SeoSetting::create(['page_key' => $pageKey, 'is_active' => true]);

        $meta = $this->pageLabels[$pageKey] ?? null;
        $pageLabel = is_array($meta) ? $meta['label'] : ($meta ?? $pageKey);

        return view('admin.seo.edit', compact('seo', 'pageKey', 'pageLabel'));
    }

    public function update(Request $request, string $pageKey)
    {
        $seo = SeoSetting::getForPage($pageKey)
            ?? SeoSetting::create(['page_key' => $pageKey, 'is_active' => true]);

        $data = $request->except(['_token', '_method', 'og_image_file', 'twitter_image_file', 'remove_og_image', 'remove_twitter_image']);

        // Robots: combine index/noindex + follow/nofollow
        if ($request->has('robots_index') && $request->has('robots_follow')) {
            $data['robots'] = $request->input('robots_index') . ', ' . $request->input('robots_follow');
            unset($data['robots_index'], $data['robots_follow']);
        }

        // OG image upload
        if ($request->hasFile('og_image_file')) {
            if ($seo->og_image) {
                Storage::disk('public')->delete($seo->og_image);
            }
            $data['og_image'] = $request->file('og_image_file')->store('seo', 'public');
        } elseif ($request->boolean('remove_og_image')) {
            if ($seo->og_image) {
                Storage::disk('public')->delete($seo->og_image);
            }
            $data['og_image'] = null;
        }

        // Twitter image upload
        if ($request->hasFile('twitter_image_file')) {
            if ($seo->twitter_image) {
                Storage::disk('public')->delete($seo->twitter_image);
            }
            $data['twitter_image'] = $request->file('twitter_image_file')->store('seo', 'public');
        } elseif ($request->boolean('remove_twitter_image')) {
            if ($seo->twitter_image) {
                Storage::disk('public')->delete($seo->twitter_image);
            }
            $data['twitter_image'] = null;
        }

        // Validate custom_schema_markup is valid JSON if provided
        if (!empty($data['custom_schema_markup'])) {
            $decoded = json_decode($data['custom_schema_markup']);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return back()->withInput()->with('error', 'El JSON-LD personalizado no es válido: ' . json_last_error_msg());
            }
        }

        $seo->update($data);

        return redirect()->route('admin.seo.edit', $pageKey)
            ->with('success', 'Configuración SEO actualizada correctamente.');
    }
}
