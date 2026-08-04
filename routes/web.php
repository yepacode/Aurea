<?php

use App\Http\Controllers\StorefrontController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\EpaycoController;
use App\Http\Controllers\StripeWebhookController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\Auth\AdminLoginController;
use App\Http\Controllers\Auth\CustomerAuthController;
use App\Http\Controllers\Auth\CustomerPasswordController;
use App\Http\Controllers\Account\AccountController;
use App\Http\Controllers\Account\AddressController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Storefront Routes
|--------------------------------------------------------------------------
*/

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
Route::post('/productos/{slug}/resena', [\App\Http\Controllers\ReviewController::class, 'store'])->name('reviews.store');

// Marcas (distribuidora)
Route::get('/marcas',         [\App\Http\Controllers\BrandController::class, 'index'])->name('brands.index');
Route::get('/marcas/{slug}',  [\App\Http\Controllers\BrandController::class, 'show'])->name('brands.show');

// Redirects 301 desde URLs legacy del esqueleto anterior
Route::permanentRedirect('/lentes', '/productos');
Route::get('/lentes/{slug}', fn (string $slug) => redirect()->route('products.show', ['slug' => $slug], 301));
Route::permanentRedirect('/que-es-la-luz-azul', '/rituales');

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
Route::post('/checkout/apply-coupon', [CheckoutController::class, 'applyCoupon'])->name('checkout.applyCoupon');
Route::post('/checkout/remove-coupon', [CheckoutController::class, 'removeCoupon'])->name('checkout.removeCoupon');

// Order tracking
Route::get('/pedido/{tracking_token}', [CheckoutController::class, 'track'])->name('order.track');
Route::post('/pedido/{tracking_token}/comprobante', [CheckoutController::class, 'uploadReceipt'])->name('order.uploadReceipt');

// Stripe webhook
Route::post('/stripe/webhook', [StripeWebhookController::class, 'handle'])->name('stripe.webhook');

// ePayco (pasarela Colombia: PSE, tarjetas, Nequi, efectivo)
Route::get('/epayco/pagar/{order}', [EpaycoController::class, 'pay'])->name('epayco.pay');
Route::match(['get', 'post'], '/epayco/respuesta', [EpaycoController::class, 'response'])->name('epayco.response');
Route::post('/epayco/confirmacion', [EpaycoController::class, 'confirmation'])->name('epayco.confirmation');

// ── Cuenta de cliente ──────────────────────────────────────────
Route::middleware('guest:customer')->group(function () {
    Route::get('/cuenta/registro', [CustomerAuthController::class, 'showRegister'])->name('customer.register');
    Route::post('/cuenta/registro', [CustomerAuthController::class, 'register'])->name('customer.register.submit');
    Route::get('/cuenta/ingresar', [CustomerAuthController::class, 'showLogin'])->name('customer.login');
    Route::post('/cuenta/ingresar', [CustomerAuthController::class, 'login'])->name('customer.login.submit');

    // Recuperar contraseña
    Route::get('/cuenta/olvide-contrasena', [CustomerPasswordController::class, 'showForgot'])->name('customer.password.request');
    Route::post('/cuenta/olvide-contrasena', [CustomerPasswordController::class, 'sendResetLink'])->name('customer.password.email');
    Route::get('/cuenta/restablecer/{token}', [CustomerPasswordController::class, 'showReset'])->name('customer.password.reset');
    Route::post('/cuenta/restablecer', [CustomerPasswordController::class, 'reset'])->name('customer.password.update');
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

    // Direcciones guardadas
    Route::get('/cuenta/direcciones', [AddressController::class, 'index'])->name('account.addresses');
    Route::post('/cuenta/direcciones', [AddressController::class, 'store'])->name('account.addresses.store');
    Route::put('/cuenta/direcciones/{address}', [AddressController::class, 'update'])->name('account.addresses.update');
    Route::patch('/cuenta/direcciones/{address}/predeterminada', [AddressController::class, 'setDefault'])->name('account.addresses.default');
    Route::delete('/cuenta/direcciones/{address}', [AddressController::class, 'destroy'])->name('account.addresses.destroy');
});

// Blog
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');

// Lead capture
Route::post('/leads', [LeadController::class, 'store'])->name('leads.store');

// "Avísame cuando vuelva" (notificación de stock)
Route::post('/avisame', [\App\Http\Controllers\StockNotificationController::class, 'store'])->name('stock.notify');

// Landing pages & Quiz
Route::get('/landing', [LandingController::class, 'quiz'])->name('landing.quiz');
Route::get('/quiz', fn () => redirect()->route('landing.quiz'));
Route::post('/landing/resultado', [LandingController::class, 'quizResult'])->name('landing.quiz.result');

// SEO
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');

// robots.txt dinámico (la URL del sitemap se adapta al dominio real)
Route::get('/robots.txt', function () {
    $lines = [
        'User-agent: *',
        'Disallow: /admin/',
        'Disallow: /carrito',
        'Disallow: /checkout',
        'Disallow: /pedido/',
        'Allow: /',
        '',
        'Sitemap: ' . url('/sitemap.xml'),
        '',
    ];

    return response(implode("\n", $lines), 200)
        ->header('Content-Type', 'text/plain; charset=UTF-8');
})->name('robots');

// Serve storage files (fallback when symlink doesn't work on shared hosting)
Route::get('/storage/{path}', function (string $path) {
    $fullPath = storage_path('app/public/' . $path);

    if (!file_exists($fullPath)) {
        abort(404);
    }

    $mime = mime_content_type($fullPath);

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
    ->middleware('guest');

Route::post('/admin/logout', [AdminLoginController::class, 'logout'])
    ->name('admin.logout')
    ->middleware('auth');
