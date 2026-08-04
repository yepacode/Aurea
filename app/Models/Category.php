<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'type_filter',
        'image',
        'sort_order',
        'promo_2x1',
        'meta_title', 'meta_description', 'meta_keywords', 'focus_keyword',
        'canonical_url', 'noindex', 'nofollow',
        'og_type', 'og_title', 'og_description', 'og_image_path',
        'twitter_card', 'twitter_title', 'twitter_description', 'twitter_image_path',
        'custom_schema_markup',
    ];

    protected $casts = [
        'promo_2x1' => 'boolean',
        'noindex'   => 'boolean',
        'nofollow'  => 'boolean',
    ];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Returns the configured product types this category links to,
     * as an array of cleaned strings. Empty if no filter is set.
     */
    public function typeFilterList(): array
    {
        if (empty($this->type_filter)) {
            return [];
        }
        return array_values(array_filter(array_map(
            fn ($t) => trim($t),
            explode(',', $this->type_filter)
        )));
    }
}
