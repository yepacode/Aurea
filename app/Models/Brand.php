<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Brand extends Model
{
    protected $fillable = [
        'name', 'slug',
        'logo_path', 'banner_path',
        'short_description', 'long_description',
        'website_url', 'country_origin',
        'is_featured', 'is_active', 'sort_order',
        'meta_title', 'meta_description', 'meta_keywords', 'focus_keyword',
        'canonical_url', 'noindex', 'nofollow',
        'og_type', 'og_title', 'og_description', 'og_image_path',
        'twitter_card', 'twitter_title', 'twitter_description', 'twitter_image_path',
        'custom_schema_markup',
        // Landing enriquecida
        'hero_image', 'hero_title', 'hero_tagline',
        'story_title', 'story_content', 'story_image',
        'pillars_json', 'featured_products_json',
        'quote_text', 'quote_author',
        'brand_color', 'landing_enabled',
    ];

    protected function casts(): array
    {
        return [
            'is_featured' => 'boolean',
            'is_active'   => 'boolean',
            'sort_order'  => 'integer',
            'noindex'     => 'boolean',
            'nofollow'    => 'boolean',
            'landing_enabled'        => 'boolean',
            'pillars_json'           => 'array',
            'featured_products_json' => 'array',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Brand $b) {
            if (! $b->slug) {
                $b->slug = Str::slug($b->name);
            }
        });
    }

    // ── Relations ──

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function activeProducts(): HasMany
    {
        return $this->products()->where('is_active', true);
    }

    // ── Scopes ──

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    // ── Helpers ──

    public function getLogoUrlAttribute(): ?string
    {
        return $this->logo_path ? asset('storage/'.$this->logo_path) : null;
    }

    public function getBannerUrlAttribute(): ?string
    {
        return $this->banner_path ? asset('storage/'.$this->banner_path) : null;
    }

    public function getHeroImageUrlAttribute(): ?string
    {
        return $this->hero_image ? asset('storage/'.$this->hero_image) : null;
    }

    public function getStoryImageUrlAttribute(): ?string
    {
        return $this->story_image ? asset('storage/'.$this->story_image) : null;
    }
}
