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

    /** Predice la categoría ML (MCO). Reintenta con el nombre acortado si falla. */
    public function predictCategory(string $title): ?string
    {
        foreach ($this->categoryQueries($title) as $q) {
            $res = Http::get(self::API.'/sites/MCO/domain_discovery/search', [
                'q'     => $q,
                'limit' => 1,
            ]);
            $cat = $res->successful() ? $res->json('0.category_id') : null;
            if ($cat) {
                return $cat;
            }
        }

        return null;
    }

    /** Variantes de consulta para el predictor: completo → antes del guión → 2 palabras → 1. */
    private function categoryQueries(string $title): array
    {
        $title = trim($title);
        $queries = [$title];

        foreach (['–', '—', '-'] as $sep) {
            if (str_contains($title, $sep)) {
                $queries[] = trim(explode($sep, $title)[0]);
            }
        }

        $words = preg_split('/\s+/', $title) ?: [];
        if (count($words) >= 2) {
            $queries[] = $words[0].' '.$words[1];
        }
        if (! empty($words[0])) {
            $queries[] = $words[0];
        }

        return array_values(array_unique(array_filter($queries)));
    }

    /** Todos los atributos de una categoría. */
    public function categoryAttributes(string $categoryId): array
    {
        $res = Http::get(self::API."/categories/{$categoryId}/attributes");

        return $res->successful() ? $res->json() : [];
    }

    /** ¿El atributo es obligatorio (incluye los condicionales)? */
    private function attributeIsRequired(array $a): bool
    {
        $tags = $a['tags'] ?? [];
        foreach (['required', 'catalog_required', 'conditional_required'] as $k) {
            if (($tags[$k] ?? false) === true) {
                return true;
            }
            if (is_array($tags) && in_array($k, $tags, true)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Precio ML. Base competitiva = precio web +5%. Si el producto tiene COSTO
     * cargado (cost_price), se eleva lo necesario para garantizar ganancia tras
     * la comisión de ML. Redondeo a 50, mínimo 2900 COP.
     */
    public function mlPrice(float $webPrice, ?float $cost = null): int
    {
        $base = $webPrice * 1.05;

        if ($cost !== null && $cost > 0) {
            $comm   = (float) config('services.mercadolibre.commission', 0.14);
            $fee    = (int) config('services.mercadolibre.fixed_fee', 1000);
            $margin = (float) config('services.mercadolibre.margin', 0.15);
            $floor  = (int) config('services.mercadolibre.margin_floor', 1200);
            $target = max($cost * $margin, $floor);                 // ganancia neta mínima
            $priceMin = ($cost + $fee + $target) / max(1 - $comm, 0.01);
            $base = max($base, $priceMin);
        }

        return max((int) (round($base / 50) * 50), 2900);
    }

    /** Costo del producto (cost_price) como float, o null si no está cargado. */
    private function productCost(Product $product): ?float
    {
        return $product->cost_price !== null && (float) $product->cost_price > 0
            ? (float) $product->cost_price
            : null;
    }

    /** Extrae un valor numérico del nombre para un atributo number/number_unit. */
    private function numericFromName(string $name, array $attr): ?string
    {
        if (($attr['value_type'] ?? '') === 'number_unit') {
            // Volumen/peso: "130ml", "250 cc", "1 L", "x15ml", "x250cc".
            if (preg_match('/(\d+(?:[.,]\d+)?)\s*(ml|mililitros|cc|lt|litros|l|gramos|gr|kg|g|oz)\b/i', $name, $m)) {
                $num  = str_replace(',', '.', $m[1]);
                $unit = $this->normalizeUnit($m[2], $attr);

                return $unit ? "{$num} {$unit}" : null;
            }

            return null;
        }

        // number: cantidad "x3", "x 5", o el primer entero del nombre.
        if (preg_match('/x\s*(\d+)/i', $name, $m)) {
            return $m[1];
        }
        if (preg_match('/(\d+)/', $name, $m)) {
            return $m[1];
        }

        return null;
    }

    /** Normaliza la unidad hallada y la valida contra las permitidas de la categoría. */
    private function normalizeUnit(string $raw, array $attr): ?string
    {
        $map = [
            'ml' => 'mL', 'mililitros' => 'mL', 'cc' => 'cc',
            'l' => 'L', 'lt' => 'L', 'litros' => 'L',
            'g' => 'g', 'gr' => 'g', 'gramos' => 'g', 'kg' => 'kg', 'oz' => 'fl oz',
        ];
        $unit = $map[mb_strtolower(trim($raw))] ?? null;
        if (! $unit) {
            return null;
        }

        // Si la categoría declara unidades permitidas, respetarlas (ej. no gramos en volumen).
        $allowed = collect($attr['allowed_units'] ?? [])
            ->pluck('id')->map(fn ($u) => mb_strtolower((string) $u))->all();
        if ($allowed && ! in_array(mb_strtolower($unit), $allowed, true)) {
            return null;
        }

        return $unit;
    }

    /** Construye la lista de atributos del item: obligatorios + enriquecidos. */
    public function buildAttributes(Product $product, string $categoryId): array
    {
        $catAttrs = $this->categoryAttributes($categoryId);

        $attrs = [['id' => 'BRAND', 'value_name' => $product->brand?->name ?: 'Genérico']];
        $added = ['BRAND' => true];

        foreach ($catAttrs as $a) {
            $id = $a['id'] ?? null;
            if (! $id || isset($added[$id]) || ! $this->attributeIsRequired($a)) {
                continue;
            }

            // GTIN (código de barras): no lo tenemos → se resuelve con EMPTY_GTIN_REASON.
            if ($id === 'GTIN') {
                continue;
            }

            // Motivo de código vacío: elegimos "no tiene código registrado".
            if ($id === 'EMPTY_GTIN_REASON') {
                $vals = $a['values'] ?? [];
                $chosen = collect($vals)->first(fn ($v) => str_contains(mb_strtolower($v['name'] ?? ''), 'no tiene código'))
                    ?? collect($vals)->first(fn ($v) => str_contains(mb_strtolower($v['name'] ?? ''), 'otra'))
                    ?? ($vals[0] ?? null);
                if ($chosen) {
                    $attrs[] = ['id' => 'EMPTY_GTIN_REASON', 'value_id' => $chosen['id']];
                    $added[$id] = true;
                }
                continue;
            }

            // Cantidad por pack: 1 (vendemos la unidad).
            if ($id === 'UNITS_PER_PACK') {
                $attrs[] = ['id' => 'UNITS_PER_PACK', 'value_name' => '1'];
                $added[$id] = true;
                continue;
            }

            if ($id === 'MODEL') {
                $attrs[] = ['id' => 'MODEL', 'value_name' => Str::limit($product->name, 50, '')];
                $added[$id] = true;
                continue;
            }

            // Atributos numéricos (volumen neto, nº de brochas, etc.): se extraen
            // del nombre del producto. Enviar el nombre como texto es inválido.
            $valueType = $a['value_type'] ?? 'string';
            if (empty($a['values']) && in_array($valueType, ['number', 'number_unit'], true)) {
                $num = $this->numericFromName($product->name, $a);
                if ($num !== null) {
                    $attrs[] = ['id' => $id, 'value_name' => $num];
                    $added[$id] = true;
                }
                // Si no se puede extraer, se omite (ese producto requerirá carga manual).
                continue;
            }

            // Con valores permitidos → el primero; si no → el nombre del producto.
            if (! empty($a['values'])) {
                $attrs[] = ['id' => $id, 'value_id' => $a['values'][0]['id']];
            } else {
                $attrs[] = ['id' => $id, 'value_name' => Str::limit($product->name, 60, '')];
            }
            $added[$id] = true;
        }

        return $this->enrichAttributes($attrs, $added, $catAttrs, $product);
    }

    /** Agrega atributos opcionales fiables para mejorar la ficha técnica (sin inventar datos). */
    private function enrichAttributes(array $attrs, array $added, array $catAttrs, Product $product): array
    {
        // SKU del vendedor desde el código interno.
        if (! isset($added['SELLER_SKU']) && filled($product->internal_code)) {
            $attrs[] = ['id' => 'SELLER_SKU', 'value_name' => (string) $product->internal_code];
            $added['SELLER_SKU'] = true;
        }

        // Atributos opcionales con lista de valores que el NOMBRE menciona explícitamente.
        $haystack = ' '.mb_strtolower($product->name).' ';
        foreach ($catAttrs as $a) {
            $id = $a['id'] ?? null;
            if (! $id || isset($added[$id]) || empty($a['values'])) {
                continue;
            }
            $tags = $a['tags'] ?? [];
            if (($tags['hidden'] ?? false) === true || ($tags['variation_attribute'] ?? false) === true) {
                continue;
            }

            $match = null;
            foreach ($a['values'] as $v) {
                $vn = mb_strtolower(trim($v['name'] ?? ''));
                if (mb_strlen($vn) < 3) {
                    continue;
                }
                if (str_contains($haystack, ' '.$vn.' ')) {
                    if ($match && ($match['id'] ?? null) !== ($v['id'] ?? null)) {
                        $match = null; // dos valores distintos coinciden → ambiguo, no arriesgar
                        break;
                    }
                    $match = $v;
                }
            }
            if ($match && ! empty($match['id'])) {
                $attrs[] = ['id' => $id, 'value_id' => $match['id']];
                $added[$id] = true;
            }
        }

        return $attrs;
    }

    /** Precio mínimo (COP) para ofrecer envío gratis; 0 = desactivado. */
    private function freeShippingMin(): int
    {
        return (int) (config('services.mercadolibre.free_shipping_min') ?? 0);
    }

    /** Arma el payload del item para un producto. */
    public function buildItem(Product $product, string $categoryId): array
    {
        $pictures = collect($product->images ?? [])
            ->map(fn ($img) => ['source' => jpg_url($img)])
            ->filter(fn ($p) => ! empty($p['source']))
            ->take(10)->values()->all();

        $price = $this->mlPrice((float) $product->price, $this->productCost($product));

        $payload = [
            // En la nueva estructura de ML, family_name reemplaza a title
            // (enviar 'title' da body.invalid_fields).
            'family_name'        => Str::limit($product->name, 60, ''),
            'category_id'        => $categoryId,
            'price'              => $price,
            'currency_id'        => 'COP',
            'available_quantity' => 10,
            'buying_mode'        => 'buy_it_now',
            'listing_type_id'    => 'gold_special',
            'condition'          => 'new',
            'pictures'           => $pictures,
            'attributes'         => $this->buildAttributes($product, $categoryId),
        ];

        if ($this->freeShippingMin() > 0 && $price >= $this->freeShippingMin()) {
            $payload['shipping'] = ['mode' => 'me2', 'free_shipping' => true];
        }

        return $payload;
    }

    /** Actualiza un item YA publicado (atributos + envío) para mejorar su calidad. */
    public function updateItem(Product $product): array
    {
        $token = $this->accessToken();
        if (! $token) {
            return [false, 'Sin token válido. Autoriza en /ml/conectar.'];
        }
        $itemId = $product->ml_item_id;
        if (! $itemId) {
            return [false, 'El producto no tiene ml_item_id.'];
        }

        // La categoría real del item (puede diferir de una nueva predicción).
        $catRes = Http::withToken($token)->get(self::API."/items/{$itemId}", ['attributes' => 'category_id']);
        $categoryId = $catRes->successful() ? $catRes->json('category_id') : null;
        if (! $categoryId) {
            return [false, 'No se pudo leer la categoría del item.'];
        }

        $price = $this->mlPrice((float) $product->price, $this->productCost($product));
        $payload = [
            'price'      => $price,
            'attributes' => $this->buildAttributes($product, $categoryId),
        ];
        if ($this->freeShippingMin() > 0 && $price >= $this->freeShippingMin()) {
            $payload['shipping'] = ['mode' => 'me2', 'free_shipping' => true];
        }

        $res = Http::withToken($token)->put(self::API."/items/{$itemId}", $payload);
        if (! $res->successful()) {
            return [false, 'ML '.$res->status().': '.$res->body()];
        }

        return [true, $itemId];
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
