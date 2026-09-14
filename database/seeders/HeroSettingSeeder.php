<?php

namespace Database\Seeders;

use App\Models\HeroSetting;
use Illuminate\Database\Seeder;

class HeroSettingSeeder extends Seeder
{
    public function run(): void
    {
        HeroSetting::updateOrCreate(['id' => 1], [
            'media_type' => 'gradient',
            'overlay_opacity' => 0.30,
            'eyebrow_text' => 'Cosmética natural',
            'title_line1' => 'Tu ritual de belleza',
            'title_line2' => 'natural, elegante',
            'title_line3' => 'y atemporal',
            'title_highlight_word' => 'atemporal',
            'subtitle' => 'Skincare, fragancias y rituales premium. Ingredientes botánicos seleccionados para realzar tu belleza natural.',
            'badge_text' => 'Envío gratis desde $150.000',
            'btn_primary_text' => 'Descubrir productos',
            'btn_primary_url' => '/lentes',
            'btn_secondary_text' => 'Hacer mi quiz de piel',
            'btn_secondary_url' => '/quiz',
            'trust_items' => [
                'Envío gratis desde $150.000',
                '30 días de devolución',
                'Ingredientes botánicos',
            ],
            'stat1_number' => '100%',
            'stat1_label' => 'ingredientes naturales',
            'stat2_number' => '8',
            'stat2_label' => 'rituales únicos',
            'is_active' => true,
        ]);
    }
}
