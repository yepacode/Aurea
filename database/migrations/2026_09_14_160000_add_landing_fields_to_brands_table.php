<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('brands', function (Blueprint $table) {
            if (! Schema::hasColumn('brands', 'hero_image')) {
                $table->string('hero_image')->nullable()->after('banner_path');
            }
            if (! Schema::hasColumn('brands', 'hero_title')) {
                $table->string('hero_title')->nullable()->after('hero_image');
            }
            if (! Schema::hasColumn('brands', 'hero_tagline')) {
                $table->text('hero_tagline')->nullable()->after('hero_title');
            }
            if (! Schema::hasColumn('brands', 'story_title')) {
                $table->string('story_title')->nullable()->after('hero_tagline');
            }
            if (! Schema::hasColumn('brands', 'story_content')) {
                $table->text('story_content')->nullable()->after('story_title');
            }
            if (! Schema::hasColumn('brands', 'story_image')) {
                $table->string('story_image')->nullable()->after('story_content');
            }
            if (! Schema::hasColumn('brands', 'pillars_json')) {
                $table->json('pillars_json')->nullable()->after('story_image');
            }
            if (! Schema::hasColumn('brands', 'featured_products_json')) {
                $table->json('featured_products_json')->nullable()->after('pillars_json');
            }
            if (! Schema::hasColumn('brands', 'quote_text')) {
                $table->text('quote_text')->nullable()->after('featured_products_json');
            }
            if (! Schema::hasColumn('brands', 'quote_author')) {
                $table->string('quote_author')->nullable()->after('quote_text');
            }
            if (! Schema::hasColumn('brands', 'brand_color')) {
                $table->string('brand_color', 9)->nullable()->after('quote_author');
            }
            if (! Schema::hasColumn('brands', 'landing_enabled')) {
                $table->boolean('landing_enabled')->default(false)->after('brand_color');
            }
        });
    }

    public function down(): void
    {
        Schema::table('brands', function (Blueprint $table) {
            $cols = [
                'hero_image','hero_title','hero_tagline',
                'story_title','story_content','story_image',
                'pillars_json','featured_products_json',
                'quote_text','quote_author',
                'brand_color','landing_enabled',
            ];
            foreach ($cols as $c) {
                if (Schema::hasColumn('brands', $c)) {
                    $table->dropColumn($c);
                }
            }
        });
    }
};
