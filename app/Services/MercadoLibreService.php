<?php

namespace App\Services;

use App\Models\MercadoLibreSetting;
use App\Models\Product;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

/**
 * Integración con la API de Mercado Libre (Colombia, site MCO).
 * - OAuth: authUrl(), exchangeCode(), accessToken() con auto-refresh.
 * - Publicación: predictCategory(), buildItem(), publish().
 *
 * Tokens en la tabla mercadolibre_settings (ver MercadoLibreSetting).
 * Credenciales en config('services.mercadolibre.*') desde el .env.
 */
class MercadoLibreService
{
    private const API  = 'https://api.mercadolibre.com';
    private const AUTH = 'https://auth.mercadolibre.com.co/authorization';

    public function configured(): bool
    {
        return filled(config('services.mercadolibre.app_id'))
            && filled(config('services.mercadolibre.secret'));
    }

    public function connected(): bool
    {
        return filled(MercadoLibreSetting::get('ml_refresh_token'));
    }

    /** URL a la que mandamos al admin para autorizar la app. */
    public function authUrl(): string
    {
        return self::AUTH.'?'.http_build_query([
            'response_type' => 'code',
            'client_id'     => config('services.mercadolibre.app_id'),
            'redirect_uri'  => config('services.mercadolibre.redirect'),
        ]);
    }

    /** Intercambia el code del callback por tokens y los guarda. */
    public function exchangeCode(string $code): bool
    {
        $res = Http::asForm()->post(self::API.'/oauth/token', [
            'grant_type'    => 'authorization_code',
            'client_id'     => config('services.mercadolibre.app_id'),
            'client_secret' => config('services.mercadolibre.secret'),
            'code'          => $code,
            'redirect_uri'  => config('services.mercadolibre.redirect'),
        ]);

        if (! $res->successful()) {
            return false;
        }
        $this->storeToken($res->json());

        return true;
    }

    private function storeToken(array $data): void
    {
        MercadoLibreSetting::set('ml_access_token', $data['access_token'] ?? '');
        if (! empty($data['refresh_token'])) {
            MercadoLibreSetting::set('ml_refresh_token', $data['refresh_token']);
        }
        // Restamos 120s de margen al expires_in (por defecto ~6h).
        MercadoLibreSetting::set('ml_expires_at', now()->addSeconds((int) ($data['expires_in'] ?? 21600) - 120)->timestamp);
        if (! empty($data['user_id'])) {
            MercadoLibreSetting::set('ml_user_id', $data['user_id']);
        }
    }

    /** Devuelve un access_token válido, refrescándolo si expiró. */
    public function accessToken(): ?string
    {
        if ((int) MercadoLibreSetting::get('ml_expires_at', 0) > now()->timestamp) {
            return MercadoLibreSetting::get('ml_access_token');
        }

        return $this->refresh();
    }

    private function refresh(): ?string
    {
        $refresh = MercadoLibreSetting::get('ml_refresh_token');
        if (! $refresh) {
            return null;
        }

        $res = Http::asForm()->post(self::API.'/oauth/token', [
            'grant_type'    => 'refresh_token',
            'client_id'     => config('services.mercadolibre.app_id'),
            'client_secret' => config('services.mercadolibre.secret'),
            'refresh_token' => $refresh,
        ]);

        if (! $res->successful()) {
            return null;
        }
        $this->storeToken($res->json());

        return MercadoLibreSetting::get('ml_access_token');
    }

    /** Predice la categoría ML (MCO) a partir del título del producto. */
    public function predictCategory(string $title): ?string
    {
        $res = Http::get(self::API.'/sites/MCO/domain_discovery/search', [
            'q'     => $title,
            'limit' => 1,
        ]);

        return $res->successful() ? $res->json('0.category_id') : null;
    }

    /** Atributos OBLIGATORIOS de una categoría. */
    public function requiredAttributes(string $categoryId): array
    {
        $res = Http::get(self::API."/categories/{$categoryId}/attributes");
        if (! $res->successful()) {
            return [];
        }

        return collect($res->json())
            ->filter(function ($a) {
                $tags = $a['tags'] ?? [];
                return ($tags['required'] ?? false) === true
                    || (is_array($tags) && in_array('required', $tags, true));
            })
            ->values()->all();
    }

    /** Precio ML: precio web +5%, redondeo a 50, MÍNIMO 2900 COP. */
    public function mlPrice(float $webPrice): int
    {
        return max((int) (round($webPrice * 1.05 / 50) * 50), 2900);
    }

    /** Arma el payload del item para un producto. */
    public function buildItem(Product $product, string $categoryId): array
    {
        $pictures = collect($product->images ?? [])
            ->map(fn ($img) => ['source' => jpg_url($img)])
            ->filter(fn ($p) => ! empty($p['source']))
            ->take(10)->values()->all();

        $attrs = [['id' => 'BRAND', 'value_name' => $product->brand?->name ?: 'Genérico']];

        foreach ($this->requiredAttributes($categoryId) as $a) {
            $id = $a['id'] ?? null;
            if (! $id || $id === 'BRAND') {
                continue;
            }
            if ($id === 'MODEL') {
                $attrs[] = ['id' => 'MODEL', 'value_name' => Str::limit($product->name, 50, '')];
                continue;
            }
            // Con valores permitidos → el primero; si no → el nombre del producto.
            if (! empty($a['values'])) {
                $attrs[] = ['id' => $id, 'value_id' => $a['values'][0]['id']];
            } else {
                $attrs[] = ['id' => $id, 'value_name' => Str::limit($product->name, 60, '')];
            }
        }

        return [
            'title'              => Str::limit($product->name, 60, ''),
            'category_id'        => $categoryId,
            'price'              => $this->mlPrice((float) $product->price),
            'currency_id'        => 'COP',
            'available_quantity' => 10,
            'buying_mode'        => 'buy_it_now',
            'listing_type_id'    => 'gold_special',
            'condition'          => 'new',
            'pictures'           => $pictures,
            'attributes'         => $attrs,
        ];
    }

    /** Publica un producto. Devuelve [bool ok, string item_id|error]. */
    public function publish(Product $product): array
    {
        $token = $this->accessToken();
        if (! $token) {
            return [false, 'Sin token válido. Autoriza en /ml/conectar.'];
        }

        $categoryId = $this->predictCategory($product->name);
        if (! $categoryId) {
            return [false, 'No se pudo predecir la categoría.'];
        }

        $payload = $this->buildItem($product, $categoryId);
        if (empty($payload['pictures'])) {
            return [false, 'El producto no tiene fotos.'];
        }

        $res = Http::withToken($token)->post(self::API.'/items', $payload);
        if (! $res->successful()) {
            return [false, 'ML '.$res->status().': '.$res->body()];
        }

        $itemId = $res->json('id');
        $product->forceFill(['ml_item_id' => $itemId])->save();

        // Descripción (recurso aparte en la API de ML).
        $desc = trim(strip_tags((string) $product->description));
        if ($desc !== '') {
            Http::withToken($token)->post(self::API."/items/{$itemId}/description", [
                'plain_text' => Str::limit($desc, 1500, ''),
            ]);
        }

        return [true, $itemId];
    }
}
