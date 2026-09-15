<?php

use App\Http\Controllers\StorefrontController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\BundleController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\EpaycoController;
use App\Http\Controllers\StripeWebhookController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\FeedController;
use App\Http\Controllers\Auth\AdminLoginController;
use App\Http\Controllers\Auth\CustomerAuthController;
use App\Http\Controllers\Auth\CustomerPasswordController;
use App\Http\Controllers\Auth\UnifiedLoginController;
use App\Http\Controllers\Account\AccountController;
use App\Http\Controllers\Account\AddressController;
use App\Http\Controllers\Account\SubscriptionsController as AccountSubscriptionsController;
use App\Http\Controllers\SubscriptionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Multi-idioma
|--------------------------------------------------------------------------
| El storefront se registra DOS veces:
|   1. Sin prefijo → idioma por defecto (español). Nombres "home", etc.
|   2. Con prefijo /en → inglés. Nombres "en.home", etc.
|
| Ambos grupos comparten el mismo closure $storefrontRoutes.
| El middleware setlocale:en fuerza el idioma en el grupo /en; el default lo
| resuelve session/cookie/browser vía el middleware global en el grupo web.
*/

// Cambio de idioma desde el switcher del navbar.
Route::post('/set-locale', [LocaleController::class, 'set'])->name('locale.set');

$storefrontRoutes = function () {

    // Home
    Route::get('/', [StorefrontController::class, 'home'])->name('home');

    // Página editorial — Rituales (antes "luz azul")
    Route::get('/rituales', [StorefrontController::class, 'blueLight'])->name('blue-light');
    // Detalle de un ritual (contenido completo, reutiliza el sistema de artículos)
    Route::get('/rituales/{slug}', [BlogController::class, 'show'])->name('ritual.show');

    // Info pages
    Route::get('/sobre-nosotras', [StorefrontController::class, 'about'])->name('about');
    Route::get('/contacto', [StorefrontController::class, 'contact'])->name('contact');
    Route::get('/envios-y-devoluciones', [StorefrontController::class, 'shippingReturns'])->name('shipping-returns');

    // Páginas legales
    Route::get('/terminos-y-condiciones', [StorefrontController::class, 'terms'])->name('legal.terms');
    Route::get('/politica-de-privacidad', [StorefrontController::class, 'privacy'])->name('legal.privacy');
    Route::get('/politica-de-cookies', [StorefrontController::class, 'cookies'])->name('legal.cookies');

    // Catálogo de productos
    Route::get('/productos', [ProductController::class, 'index'])->name('products.index');
    Route::get('/productos/{slug}', [ProductController::class, 'show'])->name('products.show');
    Route::post('/productos/{slug}/resena', [\App\Http\Controllers\ReviewController::class, 'store'])
        ->name('reviews.store')
        ->middleware('throttle:public-writes');

    // Marcas (distribuidora)
    Route::get('/marcas',         [\App\Http\Controllers\BrandController::class, 'index'])->name('brands.index');
    Route::get('/marcas/{slug}',  [\App\Http\Controllers\BrandController::class, 'show'])->name('brands.show');

    // Kits & Bundles (combos de productos con descuento)
    Route::get('/kits',                    [BundleController::class, 'index'])->name('bundles.index');
    Route::get('/kits/{slug}',             [BundleController::class, 'show'])->name('bundles.show');
    Route::post('/kits/{slug}/agregar',    [BundleController::class, 'add'])->name('bundles.add');
    Route::delete('/kits/descuento/{bundleId}', [BundleController::class, 'removeDiscount'])->name('bundles.remove-discount')
        ->whereNumber('bundleId');

    // Redirects 301 desde URLs legacy del esqueleto anterior
    Route::permanentRedirect('/lentes', '/productos');
    Route::get('/lentes/{slug}', fn (string $slug) => redirect()->route('products.show', ['slug' => $slug], 301));
    Route::permanentRedirect('/que-es-la-luz-azul', '/rituales');

    // Rituales mensuales — suscripciones recurrentes
    Route::get('/rituales-mensuales',                         [SubscriptionController::class, 'index'])->name('subscriptions.index');
    Route::get('/rituales-mensuales/{plan:slug}',             [SubscriptionController::class, 'show'])->name('subscriptions.show');
    Route::get('/rituales-mensuales/{plan:slug}/suscribirme', [SubscriptionController::class, 'subscribe'])->name('subscriptions.subscribe');
    Route::post('/rituales-mensuales/{plan:slug}/suscribirme',[SubscriptionController::class, 'store'])
        ->name('subscriptions.store')
        ->middleware(['auth:customer', 'throttle:public-writes']);

    // Cart
    Route::get('/carrito', [CartController::class, 'index'])->name('cart.index');
    Route::post('/carrito/agregar', [CartController::class, 'add'])->name('cart.add');
    Route::patch('/carrito/actualizar/{itemId}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/carrito/eliminar/{itemId}', [CartController::class, 'remove'])->name('cart.remove');

    // Checkout
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'process'])->name('checkout.process');
    Route::get('/checkout/confirmacion/{order}', [CheckoutController::class, 'confirmation'])->name('checkout.confirmation');
    Route::post('/checkout/create-payment-intent', [CheckoutController::class, 'createPaymentIntent'])->name('checkout.createPaymentIntent');
    Route::post('/checkout/calculate-shipping', [CheckoutController::class, 'calculateShipping'])->name('checkout.calculateShipping');
    Route::post('/checkout/shipping-quote', [CheckoutController::class, 'shippingQuote'])->name('checkout.shippingQuote');
    Route::post('/checkout/apply-coupon', [CheckoutController::class, 'applyCoupon'])
        ->name('checkout.applyCoupon')
        ->middleware('throttle:public-writes');
    Route::post('/checkout/remove-coupon', [CheckoutController::class, 'removeCoupon'])->name('checkout.removeCoupon');

    // Order tracking
    Route::get('/pedido/{tracking_token}', [CheckoutController::class, 'track'])->name('order.track');
    Route::post('/pedido/{tracking_token}/comprobante', [CheckoutController::class, 'uploadReceipt'])->name('order.uploadReceipt');
    Route::get('/pedido/{tracking_token}/pagar', [CheckoutController::class, 'payFromToken'])->name('order.pay-from-token');

    // ePayco pasarela (cliente)
    Route::get('/epayco/pagar/{order}', [EpaycoController::class, 'pay'])->name('epayco.pay');
    Route::match(['get', 'post'], '/epayco/respuesta', [EpaycoController::class, 'response'])->name('epayco.response');

    // ── Login unificado ────────────────────────────────────────────
    Route::middleware('guest:web,customer')->group(function () {
        Route::get('/ingresar', [UnifiedLoginController::class, 'showLogin'])->name('login');
        Route::post('/ingresar', [UnifiedLoginController::class, 'login'])
            ->name('login.submit')
            ->middleware('throttle:auth-attempts');
    });
    Route::post('/salir', [UnifiedLoginController::class, 'logout'])->name('logout');

    // ── Cuenta de cliente ──────────────────────────────────────────
    Route::middleware('guest:customer')->group(function () {
        Route::get('/cuenta/registro', [CustomerAuthController::class, 'showRegister'])->name('customer.register');
        Route::post('/cuenta/registro', [CustomerAuthController::class, 'register'])
            ->name('customer.register.submit')
            ->middleware('throttle:auth-attempts');
        Route::get('/cuenta/ingresar', [CustomerAuthController::class, 'showLogin'])->name('customer.login');
        Route::post('/cuenta/ingresar', [CustomerAuthController::class, 'login'])
            ->name('customer.login.submit')
            ->middleware('throttle:auth-attempts');

        // Recuperar contraseña
        Route::get('/cuenta/olvide-contrasena', [CustomerPasswordController::class, 'showForgot'])->name('customer.password.request');
        Route::post('/cuenta/olvide-contrasena', [CustomerPasswordController::class, 'sendResetLink'])
            ->name('customer.password.email')
            ->middleware('throttle:auth-attempts');
        Route::get('/cuenta/restablecer/{token}', [CustomerPasswordController::class, 'showReset'])->name('customer.password.reset');
        Route::post('/cuenta/restablecer', [CustomerPasswordController::class, 'reset'])
            ->name('customer.password.update')
            ->middleware('throttle:auth-attempts');
    });

    Route::middleware('auth:customer')->group(function () {
        Route::post('/cuenta/salir', [CustomerAuthController::class, 'logout'])->name('customer.logout');
        Route::get('/cuenta', [AccountController::class, 'dashboard'])->name('account.dashboard');
        Route::get('/cuenta/pedidos', [AccountController::class, 'orders'])->name('account.orders');
        Route::get('/cuenta/pedidos/{order}', [AccountController::class, 'order'])->name('account.order');
        Route::get('/cuenta/perfil', [AccountController::class, 'profile'])->name('account.profile');
        Route::put('/cuenta/perfil', [AccountController::class, 'updateProfile'])->name('account.profile.update');

        // Favoritos (wishlist)
        Route::get('/cuenta/favoritos', [\App\Http\Controllers\Account\WishlistController::class, 'index'])->name('account.wishlist');
        Route::post('/favoritos/toggle', [\App\Http\Controllers\Account\WishlistController::class, 'toggle'])->name('wishlist.toggle');
        Route::delete('/cuenta/favoritos/{product}', [\App\Http\Controllers\Account\WishlistController::class, 'destroy'])->name('wishlist.destroy');

        // Programa de puntos (fase 1: solo ganancia y visibilidad)
        Route::get('/cuenta/puntos', [\App\Http\Controllers\Account\LoyaltyController::class, 'index'])
            ->name('account.loyalty');

        // Programa "Recomienda y gana"
        Route::get('/cuenta/referidos', [\App\Http\Controllers\Account\ReferralsController::class, 'index'])
            ->name('account.referrals');

        // Programa de mayoristas / distribuidoras
        Route::get('/cuenta/mayorista/solicitar', [\App\Http\Controllers\Account\WholesaleController::class, 'show'])
            ->name('account.wholesale.request');
        Route::post('/cuenta/mayorista/solicitar', [\App\Http\Controllers\Account\WholesaleController::class, 'submit'])
            ->name('account.wholesale.submit')
            ->middleware('throttle:public-writes');

        // Suscripciones "rituales mensuales"
        Route::get('/cuenta/suscripciones',                    [AccountSubscriptionsController::class, 'index'])->name('account.subscriptions');
        Route::get('/cuenta/suscripciones/{subscription}',     [AccountSubscriptionsController::class, 'show'])->name('account.subscriptions.show');
        Route::patch('/cuenta/suscripciones/{subscription}/pausar',   [AccountSubscriptionsController::class, 'pause'])->name('account.subscriptions.pause');
        Route::patch('/cuenta/suscripciones/{subscription}/reanudar', [AccountSubscriptionsController::class, 'resume'])->name('account.subscriptions.resume');
        Route::delete('/cuenta/suscripciones/{subscription}',         [AccountSubscriptionsController::class, 'cancel'])->name('account.subscriptions.cancel');
        Route::put('/cuenta/suscripciones/{subscription}/direccion',  [AccountSubscriptionsController::class, 'updateAddress'])->name('account.subscriptions.address');

        // Direcciones guardadas
        Route::get('/cuenta/direcciones', [AddressController::class, 'index'])->name('account.addresses');
        Route::post('/cuenta/direcciones', [AddressController::class, 'store'])->name('account.addresses.store');
        Route::put('/cuenta/direcciones/{address}', [AddressController::class, 'update'])->name('account.addresses.update');
        Route::patch('/cuenta/direcciones/{address}/predeterminada', [AddressController::class, 'setDefault'])->name('account.addresses.default');
        Route::delete('/cuenta/direcciones/{address}', [AddressController::class, 'destroy'])->name('account.addresses.destroy');
    });

    // Blog → redirects 301
    Route::redirect('/blog', '/rituales', 301)->name('blog.index');
    Route::get('/blog/{slug}', fn (string $slug) => redirect()->route('ritual.show', ['slug' => $slug], 301))
        ->name('blog.show');

    // Lead capture
    Route::post('/leads', [LeadController::class, 'store'])
        ->name('leads.store')
        ->middleware('throttle:public-writes');

    // "Avísame cuando vuelva"
    Route::post('/avisame', [\App\Http\Controllers\StockNotificationController::class, 'store'])
        ->name('stock.notify')
        ->middleware('throttle:public-writes');

    // Web Push notifications
    Route::get('/push/public-key', [\App\Http\Controllers\PushController::class, 'publicKey'])
        ->name('push.publicKey');
    Route::post('/push/subscribe', [\App\Http\Controllers\PushController::class, 'subscribe'])
        ->name('push.subscribe')
        ->middleware('throttle:public-writes');
    Route::post('/push/unsubscribe', [\App\Http\Controllers\PushController::class, 'unsubscribe'])
        ->name('push.unsubscribe')
        ->middleware('throttle:public-writes');

    // NPS post-compra
    Route::get('/nps/gracias/{token}', [\App\Http\Controllers\NpsController::class, 'thanks'])
        ->name('nps.thanks')
        ->where('token', '[A-Za-z0-9]{40}');
    Route::get('/nps/{token}/{score?}', [\App\Http\Controllers\NpsController::class, 'respond'])
        ->name('nps.respond')
        ->where(['token' => '[A-Za-z0-9]{40}', 'score' => '[0-9]|10']);
    Route::post('/nps/{token}', [\App\Http\Controllers\NpsController::class, 'submit'])
        ->name('nps.submit')
        ->where('token', '[A-Za-z0-9]{40}')
        ->middleware('throttle:public-writes');

    // FAQ — chatbot flotante + página SEO
    Route::get('/preguntas-frecuentes', [\App\Http\Controllers\FaqController::class, 'index'])->name('faq.index');
    Route::get('/faq/categorias',        [\App\Http\Controllers\FaqController::class, 'categoriesJson'])->name('faq.categories.json');
    Route::get('/faq/categoria/{slug}',  [\App\Http\Controllers\FaqController::class, 'categoryJson'])->name('faq.category.json');
    Route::get('/faq/buscar',            [\App\Http\Controllers\FaqController::class, 'search'])->name('faq.search');
    Route::post('/faq/{id}/util',        [\App\Http\Controllers\FaqController::class, 'helpful'])
        ->name('faq.helpful')
        ->whereNumber('id')
        ->middleware('throttle:public-writes');
    Route::post('/faq/{id}/view',        [\App\Http\Controllers\FaqController::class, 'view'])
        ->name('faq.view')
        ->whereNumber('id')
        ->middleware('throttle:public-writes');

    // Landing pages & Quiz
    Route::get('/landing', [LandingController::class, 'quiz'])->name('landing.quiz');
    Route::get('/quiz', fn () => redirect()->route('landing.quiz'));
    // Alias amigable/SEO — /quiz-de-piel apunta al landing del quiz.
    Route::get('/quiz-de-piel', fn () => redirect()->route('landing.quiz'));
    Route::post('/landing/resultado', [LandingController::class, 'quizResult'])->name('landing.quiz.result');
};

/*
|--------------------------------------------------------------------------
| Storefront — español (sin prefijo, default)
|--------------------------------------------------------------------------
*/
Route::group([], $storefrontRoutes);

/*
|--------------------------------------------------------------------------
| Storefront — inglés (prefijo /en, nombres "en.*")
|--------------------------------------------------------------------------
| Fuerza el locale a "en" vía middleware parametrizado para que la elección
| explícita en la URL siempre gane sobre sesión/cookie/browser.
*/
Route::prefix('en')
    ->name('en.')
    ->middleware('setlocale:en')
    ->group($storefrontRoutes);

/*
|--------------------------------------------------------------------------
| Rutas NO storefront (webhooks, admin, SEO, storage) — sin duplicar
|--------------------------------------------------------------------------
*/

// Stripe webhook
Route::post('/stripe/webhook', [StripeWebhookController::class, 'handle'])->name('stripe.webhook');

// ePayco webhook de confirmación
Route::post('/epayco/confirmacion', [EpaycoController::class, 'confirmation'])->name('epayco.confirmation');

// SEO
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');

// Google Merchant Center — feed de productos (RSS 2.0 + namespace g:)
Route::get('/feed/google-merchant.xml', [FeedController::class, 'googleMerchant'])->name('feed.google_merchant');

// robots.txt dinámico (la URL del sitemap se adapta al dominio real)
Route::get('/robots.txt', function () {
    $lines = [
        'User-agent: *',
        'Disallow: /admin/',
        'Disallow: /carrito',
        'Disallow: /checkout',
        'Disallow: /pedido/',
        'Disallow: /en/carrito',
        'Disallow: /en/checkout',
        'Disallow: /en/pedido/',
        'Allow: /',
        '',
        'Sitemap: ' . url('/sitemap.xml'),
        '',
        '# Google Merchant Center product feed',
        'Allow: /feed/google-merchant.xml',
        '',
    ];

    return response(implode("\n", $lines), 200)
        ->header('Content-Type', 'text/plain; charset=UTF-8');
})->name('robots');

// Serve storage files (fallback when symlink doesn't work on shared hosting)
Route::get('/storage/{path}', function (string $path) {
    // Rechazar caracteres peligrosos ANTES de cualquier stat.
    // Bloquea backslashes de Windows, drive letters tipo "C:" y cualquier ".."
    // (aunque el router de Laravel normaliza "..", defendemos en profundidad).
    if (preg_match('/[\\\\:]/', $path) || str_contains($path, '..')) {
        abort(403);
    }

    $baseDir = realpath(storage_path('app/public'));
    if ($baseDir === false) {
        abort(404);
    }

    $fullPath = realpath($baseDir . DIRECTORY_SEPARATOR . $path);

    // realpath devuelve false si no existe; también validamos que quede DENTRO
    // de base (previene traversal aunque el filesystem resuelva algún link).
    if ($fullPath === false || ! str_starts_with($fullPath, $baseDir . DIRECTORY_SEPARATOR)) {
        abort(404);
    }

    if (! is_file($fullPath)) {
        abort(404);
    }

    $mime = @mime_content_type($fullPath) ?: 'application/octet-stream';

    return response()->file($fullPath, [
        'Content-Type' => $mime,
        'Cache-Control' => 'public, max-age=31536000',
    ]);
})->where('path', '.*')->name('storage.serve');

/*
|--------------------------------------------------------------------------
| Admin Authentication (outside admin middleware group)
|--------------------------------------------------------------------------
*/

Route::get('/admin/login', [AdminLoginController::class, 'showLoginForm'])
    ->name('admin.login')
    ->middleware('guest');

Route::post('/admin/login', [AdminLoginController::class, 'login'])
    ->name('admin.login.submit')
    ->middleware(['guest', 'throttle:auth-attempts']);

Route::post('/admin/logout', [AdminLoginController::class, 'logout'])
    ->name('admin.logout')
    ->middleware('auth');
