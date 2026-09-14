<?php

namespace App\Providers;

use App\Models\DiscountCode;
use App\Models\PaymentSetting;
use App\Models\Product;
use App\Models\ShippingSetting;
use App\Services\CartService;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Fuerza HTTPS en URLs generadas cuando estamos detrás del proxy en
        // producción (Hostinger termina TLS pero pasa el request como http).
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        $this->applyStripeSettingsFromDatabase();

        // Avisos "vuelve a estar disponible" al reponer stock.
        Product::observe(\App\Observers\ProductObserver::class);
        \App\Models\ProductVariant::observe(\App\Observers\ProductVariantObserver::class);

        // ── Rate limiters de endpoints públicos sensibles ──
        // - auth-attempts: bloquea fuerza bruta contra login/registro/reset.
        // - public-writes: contiene abuso de leads, avísame, reseñas, cupones.
        RateLimiter::for('auth-attempts', function (Request $r) {
            return Limit::perMinute(5)->by($r->ip().'|'.(string) $r->input('email', ''));
        });
        RateLimiter::for('public-writes', function (Request $r) {
            return Limit::perMinute(20)->by($r->ip());
        });

        View::composer('partials.navbar', function ($view) {
            $cart = app(CartService::class);
            $items = $cart->getItems();
            $promo = $cart->calculate2x1();
            $subtotal = $cart->getSubtotal();
            $discount = $promo['discount'];
            $subtotalConDescuento = $subtotal - $discount;

            $threshold = (float) ShippingSetting::get('free_shipping_threshold', 999);
            $defaultShipping = (float) ShippingSetting::get('default_price', 99.00);
            $shipping = ($threshold > 0 && $subtotalConDescuento >= $threshold) ? 0 : $defaultShipping;

            $itemsJson = $items->map(fn ($item) => [
                'key' => $item['key'],
                'name' => $item['product']->name,
                'slug' => $item['product']->slug,
                'image' => $item['product']->images[0] ?? null,
                'variant' => $item['variant']
                    ? trim(($item['variant']->color ?? $item['variant']->value) . ' ' . ($item['variant']->graduation ?? ''))
                    : null,
                'type' => $item['product']->type,
                'qty' => $item['qty'],
                'unit_price' => $item['unit_price'],
                'total' => $item['total'],
            ])->values();

            // Coupon from session
            $couponCode = null;
            $couponDescription = null;
            $couponDiscount = 0;
            $discountCodeId = session('discount_code_id');

            if ($discountCodeId) {
                $discountCode = DiscountCode::find($discountCodeId);
                if ($discountCode && $discountCode->isValid($subtotalConDescuento)) {
                    $couponCode = $discountCode->code;
                    $couponDiscount = $discountCode->calculateDiscount($subtotalConDescuento);
                    $couponDescription = $discountCode->type === 'percentage'
                        ? $discountCode->value . '% de descuento'
                        : '$' . number_format($discountCode->value, 0, ',', '.') . ' de descuento';
                } else {
                    session()->forget('discount_code_id');
                }
            }

            $view->with('cartCount', $cart->count());
            $view->with('cartItemsJson', $itemsJson);
            $view->with('cartSubtotal', $subtotal);
            $view->with('cartDiscount2x1', $discount);
            $view->with('cartFreeItems', $promo['free_items']);
            $view->with('cartCouponCode', $couponCode);
            $view->with('cartCouponDescription', $couponDescription);
            $view->with('cartCouponDiscount', $couponDiscount);
            $view->with('cartShipping', $shipping);
            $view->with('cartFreeThreshold', $threshold);
            $view->with('cartTotal', max(0, $subtotalConDescuento - $couponDiscount + $shipping));
            // Nota: se retiró la consulta de "toallitas" (dead code de la etapa
            // óptica: no aplica a belleza y cargaba productos en cada request).
        });
    }

    /**
     * Sobrescribe las llaves de Stripe (config('services.stripe.*')) con los
     * valores guardados en el panel de admin (tabla payment_settings). Si un
     * valor no está configurado, se conserva el del .env como respaldo.
     */
    private function applyStripeSettingsFromDatabase(): void
    {
        try {
            if (! Schema::hasTable('payment_settings')) {
                return;
            }
        } catch (\Throwable $e) {
            // BD no disponible (p.ej. durante instalación); usar .env.
            return;
        }

        $map = [
            'stripe_key' => 'services.stripe.key',
            'stripe_secret' => 'services.stripe.secret',
            'stripe_webhook_secret' => 'services.stripe.webhook_secret',
        ];

        foreach ($map as $dbKey => $configKey) {
            $value = PaymentSetting::get($dbKey);
            if (! empty($value)) {
                config([$configKey => $value]);
            }
        }
    }
}
