<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Nivela el SEO por-ítem de productos, marcas, categorías y rituales (blog_posts)
 * al mismo detalle que el editor de páginas (seo_settings):
 * meta keywords, canónica, robots (noindex/nofollow), Open Graph completo,
 * Twitter Card completo, JSON-LD personalizado y palabra clave.
 */
return new class extends Migration
{
    /**
     * Columnas SEO objetivo. Se agregan solo las que falten en cada tabla.
     */
    private function seoColumns(): array
    {
        return [
            'meta_title'          => ['string', ['nullable' => true]],
            'meta_description'    => ['text',   ['nullable' => true]],
            'meta_keywords'       => ['string', ['nullable' => true]],
            'focus_keyword'       => ['string', ['nullable' => true, 'length' => 120]],
            'canonical_url'       => ['string', ['nullable' => true]],
            'noindex'             => ['boolean', ['default' => false]],
            'nofollow'            => ['boolean', ['default' => false]],
            'og_type'             => ['string', ['nullable' => true]],
            'og_title'            => ['string', ['nullable' => true]],
            'og_description'      => ['text',   ['nullable' => true]],
            'og_image_path'       => ['string', ['nullable' => true]],
            'twitter_card'        => ['string', ['nullable' => true]],
            'twitter_title'       => ['string', ['nullable' => true]],
            'twitter_description' => ['text',   ['nullable' => true]],
            'twitter_image_path'  => ['string', ['nullable' => true]],
            'custom_schema_markup' => ['text',  ['nullable' => true]],
        ];
    }

    public function up(): void
    {
        foreach (['products', 'brands', 'categories', 'blog_posts'] as $table) {
            Schema::table($table, function (Blueprint $t) use ($table) {
                foreach ($this->seoColumns() as $name => [$type, $opts]) {
                    if (Schema::hasColumn($table, $name)) {
                        continue;
                    }
                    // blog_posts ya usa 'og_image' en vez de 'og_image_path'
                    if ($name === 'og_image_path' && Schema::hasColumn($table, 'og_image')) {
                        continue;
                    }

                    $col = match ($type) {
                        'text'    => $t->text($name),
                        'boolean' => $t->boolean($name),
                        default   => isset($opts['length'])
                            ? $t->string($name, $opts['length'])
                            : $t->string($name),
                    };

                    if (! empty($opts['nullable'])) {
                        $col->nullable();
                    }
                    if (array_key_exists('default', $opts)) {
                        $col->default($opts['default']);
                    }
                }
            });
        }
    }

    public function down(): void
    {
        // Aditiva: se conservan los datos. No se revierten columnas
        // que ya existían antes de esta migración.
        $added = [
            'products'   => ['meta_keywords', 'canonical_url', 'nofollow', 'og_type', 'og_title', 'og_description', 'twitter_card', 'twitter_title', 'twitter_description', 'twitter_image_path', 'custom_schema_markup'],
            'brands'     => ['meta_keywords', 'focus_keyword', 'canonical_url', 'noindex', 'nofollow', 'og_type', 'og_title', 'og_description', 'og_image_path', 'twitter_card', 'twitter_title', 'twitter_description', 'twitter_image_path', 'custom_schema_markup'],
            'categories' => ['meta_title', 'meta_description', 'meta_keywords', 'focus_keyword', 'canonical_url', 'noindex', 'nofollow', 'og_type', 'og_title', 'og_description', 'og_image_path', 'twitter_card', 'twitter_title', 'twitter_description', 'twitter_image_path', 'custom_schema_markup'],
            'blog_posts' => ['meta_keywords', 'noindex', 'nofollow', 'og_type', 'twitter_card', 'twitter_title', 'twitter_description', 'twitter_image_path', 'custom_schema_markup'],
        ];

        foreach ($added as $table => $cols) {
            Schema::table($table, function (Blueprint $t) use ($table, $cols) {
                foreach ($cols as $c) {
                    if (Schema::hasColumn($table, $c)) {
                        $t->dropColumn($c);
                    }
                }
            });
        }
    }
};
