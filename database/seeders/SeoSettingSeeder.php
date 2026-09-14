<?php

namespace Database\Seeders;

use App\Models\SeoSetting;
use Illuminate\Database\Seeder;

class SeoSettingSeeder extends Seeder
{
    public function run(): void
    {
        SeoSetting::updateOrCreate(['page_key' => 'home'], [
            'meta_title' => 'Belleza Áurea | Cosmética natural, elegante y atemporal',
            'meta_description' => 'Skincare, fragancias y rituales premium con ingredientes botánicos. Belleza natural, elegante y atemporal. Envío gratis desde $150.000.',
            'meta_keywords' => 'skincare natural, cosmética botánica, perfume artesanal, ritual de belleza, belleza áurea, vitamina C, rosa mosqueta',
            'robots' => 'index, follow',
            'og_type' => 'website',
            'og_title' => 'Belleza Áurea | Tu ritual de belleza natural',
            'og_description' => 'Skincare y fragancias premium con ingredientes botánicos. Belleza natural, elegante y atemporal.',
            'twitter_card' => 'summary_large_image',
            'twitter_title' => 'Belleza Áurea | Tu ritual de belleza natural',
            'twitter_description' => 'Skincare y fragancias premium con ingredientes botánicos. Belleza natural, elegante y atemporal.',
            'is_active' => true,
        ]);
    }
}
