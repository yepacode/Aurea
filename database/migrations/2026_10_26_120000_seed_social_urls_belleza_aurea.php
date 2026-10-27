<?php

use App\Models\ContactPageSetting;
use Illuminate\Database\Migrations\Migration;

/**
 * Siembra los perfiles sociales verificados de Belleza Áurea en la
 * configuración de la página de contacto. Estos valores alimentan tanto
 * los íconos del footer como el `sameAs` del schema Organization/Store.
 *
 * Idempotente y conservador: solo rellena un campo si está vacío, para no
 * pisar lo que el admin haya configurado manualmente. TikTok se omite a
 * propósito (la cuenta aún no existe al momento de esta migración).
 */
return new class extends Migration
{
    public function up(): void
    {
        // Evita fallar si la tabla aún no existe en algún entorno.
        if (! \Illuminate\Support\Facades\Schema::hasTable('contact_page_settings')) {
            return;
        }

        $defaults = [
            'facebook_url'  => 'https://www.facebook.com/profile.php?id=61592779637444',
            'instagram_url' => 'https://www.instagram.com/bellezaaureaco',
        ];

        $setting = ContactPageSetting::getCurrent();

        foreach ($defaults as $field => $url) {
            if (blank($setting->{$field})) {
                $setting->{$field} = $url;
            }
        }

        $setting->save();
    }

    public function down(): void
    {
        // No revertimos: borrar enlaces sociales no aporta valor y podría
        // eliminar datos que el admin ajustó después.
    }
};
