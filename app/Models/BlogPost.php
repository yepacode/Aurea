<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BlogPost extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'content',
        'excerpt',
        'image',
        'featured_image_alt',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'focus_keyword',
        'category',
        'canonical_url',
        'noindex',
        'nofollow',
        'og_type',
        'og_title',
        'og_description',
        'og_image',
        'twitter_card',
        'twitter_title',
        'twitter_description',
        'twitter_image_path',
        'custom_schema_markup',
        'reading_time',
        'status',
        'published_at',
        'author_name',
        'schema_type',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'reading_time' => 'integer',
            'noindex'      => 'boolean',
            'nofollow'     => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (BlogPost $post) {
            // Auto-generate slug from title
            if (! $post->slug || $post->isDirty('title')) {
                $post->slug = Str::slug($post->title);
            }

            // Auto-calculate reading time (200 words/min)
            if ($post->isDirty('content') && $post->content) {
                $wordCount = str_word_count(strip_tags($post->content));
                $post->reading_time = max(1, (int) ceil($wordCount / 200));
            }
        });
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }
}
