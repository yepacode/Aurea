<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Http\Request;

/**
 * Manejo uniforme de los campos SEO por-ítem (producto, marca, categoría, ritual).
 * Los formularios incluyen resources/views/admin/partials/seo-panel.blade.php,
 * y los controladores fusionan seoInput() en su arreglo de datos guardados.
 */
trait HandlesSeoInput
{
    /**
     * Reglas de validación para todos los campos SEO del panel.
     */
    protected function seoRules(): array
    {
        return [
            'meta_title'           => 'nullable|string|max:255',
            'meta_description'     => 'nullable|string|max:500',
            'meta_keywords'        => 'nullable|string|max:255',
            'focus_keyword'        => 'nullable|string|max:120',
            'canonical_url'        => 'nullable|url|max:255',
            'noindex'              => 'boolean',
            'nofollow'             => 'boolean',
            'og_type'              => 'nullable|string|max:40',
            'og_title'             => 'nullable|string|max:255',
            'og_description'       => 'nullable|string|max:500',
            'og_image'             => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'twitter_card'         => 'nullable|string|max:40',
            'twitter_title'        => 'nullable|string|max:255',
            'twitter_description'  => 'nullable|string|max:500',
            'twitter_image'        => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'custom_schema_markup' => 'nullable|string|max:20000',
        ];
    }

    /**
     * Construye el arreglo de datos SEO listo para create()/update().
     *
     * @param  string  $ogCol  columna destino de la imagen OG (og_image_path u og_image)
     * @param  string  $twCol  columna destino de la imagen Twitter
     */
    protected function seoInput(Request $request, string $ogCol = 'og_image_path', string $twCol = 'twitter_image_path'): array
    {
        $data = [
            'meta_title'           => $request->input('meta_title') ?: null,
            'meta_description'     => $request->input('meta_description') ?: null,
            'meta_keywords'        => $request->input('meta_keywords') ?: null,
            'focus_keyword'        => $request->input('focus_keyword') ?: null,
            'canonical_url'        => $request->input('canonical_url') ?: null,
            'noindex'              => $request->boolean('noindex'),
            'nofollow'             => $request->boolean('nofollow'),
            'og_type'              => $request->input('og_type') ?: null,
            'og_title'             => $request->input('og_title') ?: null,
            'og_description'       => $request->input('og_description') ?: null,
            'twitter_card'         => $request->input('twitter_card') ?: null,
            'twitter_title'        => $request->input('twitter_title') ?: null,
            'twitter_description'  => $request->input('twitter_description') ?: null,
            'custom_schema_markup' => $request->input('custom_schema_markup') ?: null,
        ];

        if ($request->hasFile('og_image')) {
            $data[$ogCol] = $request->file('og_image')->store('og', 'public');
        }
        if ($request->hasFile('twitter_image')) {
            $data[$twCol] = $request->file('twitter_image')->store('og', 'public');
        }

        return $data;
    }
}
