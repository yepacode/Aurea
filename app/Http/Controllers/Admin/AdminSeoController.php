<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SeoSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminSeoController extends Controller
{
    private array $pageLabels = [
        'home' => 'Inicio',
        'products-index' => 'Catálogo',
        'blue-light' => 'Rituales',
        'blog' => 'Blog',
        'contact' => 'Contacto',
        'shipping-returns' => 'Envíos y devoluciones',
        'quiz' => 'Quiz de piel',
        'brands' => 'Marcas',
    ];

    public function index()
    {
        $pages = [];
        foreach ($this->pageLabels as $key => $label) {
            $seo = SeoSetting::getForPage($key);
            $pages[] = [
                'key' => $key,
                'label' => $label,
                'configured' => $seo && $seo->meta_title,
                'meta_title' => $seo->meta_title ?? null,
            ];
        }

        return view('admin.seo.index', compact('pages'));
    }

    public function edit(string $pageKey)
    {
        $seo = SeoSetting::getForPage($pageKey)
            ?? SeoSetting::create(['page_key' => $pageKey, 'is_active' => true]);

        $pageLabel = $this->pageLabels[$pageKey] ?? $pageKey;

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
