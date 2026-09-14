<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('analytics_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // Valores por defecto: se toman del .env si existen, para que si el
        // admin ya los tenía cargados en el entorno no se le pierdan al migrar.
        $defaults = [
            'ga4_measurement_id' => env('GA4_MEASUREMENT_ID', ''),
            'meta_pixel_id'      => env('META_PIXEL_ID', ''),
        ];

        foreach ($defaults as $key => $value) {
            DB::table('analytics_settings')->insert([
                'key' => $key,
                'value' => $value,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('analytics_settings');
    }
};
