<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductAdminController;
use App\Http\Controllers\Admin\ProductImportController;
use App\Http\Controllers\Admin\ProductImageImportController;
use App\Http\Controllers\Admin\ProductExportController;
use App\Http\Controllers\Admin\OrderAdminController;
use App\Http\Controllers\Admin\LeadAdminController;
use App\Http\Controllers\Admin\BlogAdminController;
use App\Http\Controllers\Admin\CategoryAdminController;
use App\Http\Controllers\Admin\DiscountCodeAdminController;
use App\Http\Controllers\Admin\AdminHeroController;
use App\Http\Controllers\Admin\AdminBlogPageController;
use App\Http\Controllers\Admin\AdminBlueLightPageController;
use App\Http\Controllers\Admin\AdminContactPageController;
use App\Http\Controllers\Admin\AdminQuizPageController;
use App\Http\Controllers\Admin\AdminShippingReturnsPageController;
use App\Http\Controllers\Admin\BankTransferAdminController;
use App\Http\Controllers\Admin\AdminHomePageController;
use App\Http\Controllers\Admin\AdminLentesPageController;
use App\Http\Controllers\Admin\InfographicAdminController;
use App\Http\Controllers\Admin\AdminSeoController;
use App\Http\Controllers\Admin\ShippingAdminController;
use App\Http\Controllers\Admin\TestimonialAdminController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Panel Routes
|--------------------------------------------------------------------------
| Prefix: /admin
| Name prefix: admin.
| Middleware: web, auth, admin
|--------------------------------------------------------------------------
*/

// Dashboard
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// Categories CRUD
Route::resource('categories', CategoryAdminController::class)->except(['show']);

// Brands CRUD
Route::resource('brands', \App\Http\Controllers\Admin\BrandAdminController::class)->except(['show']);

// Products CRUD
Route::get('products/import',          [ProductImportController::class, 'show'])->name('products.import');
Route::post('products/import',         [ProductImportController::class, 'store'])->name('products.import.store');
Route::get('products/import-template', [ProductExportController::class, 'template'])->name('products.import-template');
Route::get('products/export',          [ProductExportController::class, 'export'])->name('products.export');
Route::get('products/import-images',   [ProductImageImportController::class, 'show'])->name('products.import-images');
Route::post('products/import-images',  [ProductImageImportController::class, 'store'])->name('products.import-images.store');
Route::resource('products', ProductAdminController::class);
Route::patch('products/{product}/toggle', [ProductAdminController::class, 'toggle'])->name('products.toggle');

// Orders management
Route::get('orders/export/csv', [OrderAdminController::class, 'exportCsv'])->name('orders.export');
Route::resource('orders', OrderAdminController::class)->only(['index', 'create', 'store', 'show', 'update', 'destroy']);
Route::patch('orders/{order}/status', [OrderAdminController::class, 'updateStatus'])->name('orders.status');
Route::patch('orders/{order}/tracking', [OrderAdminController::class, 'updateTracking'])->name('orders.tracking');
Route::patch('orders/{order}/verify-payment', [OrderAdminController::class, 'verifyPayment'])->name('orders.verify-payment');
Route::patch('orders/{order}/reject-payment', [OrderAdminController::class, 'rejectPayment'])->name('orders.reject-payment');
Route::patch('orders/{order}/payment-status', [OrderAdminController::class, 'updatePaymentStatus'])->name('orders.payment-status');

// Leads management
Route::get('leads/export/csv', [LeadAdminController::class, 'exportCsv'])->name('leads.export');
Route::resource('leads', LeadAdminController::class)->only(['index', 'show', 'destroy']);

// Blog CRUD
Route::resource('blog', BlogAdminController::class);

// Discount codes CRUD
Route::resource('discount-codes', DiscountCodeAdminController::class)->except(['show']);

// Reseñas (moderación)
Route::get('reviews', [\App\Http\Controllers\Admin\AdminReviewController::class, 'index'])->name('reviews.index');
Route::put('reviews/{review}/approve', [\App\Http\Controllers\Admin\AdminReviewController::class, 'approve'])->name('reviews.approve');
Route::put('reviews/{review}/unapprove', [\App\Http\Controllers\Admin\AdminReviewController::class, 'unapprove'])->name('reviews.unapprove');
Route::delete('reviews/{review}', [\App\Http\Controllers\Admin\AdminReviewController::class, 'destroy'])->name('reviews.destroy');

// Shipping management
Route::get('shipping', [ShippingAdminController::class, 'index'])->name('shipping.index');
Route::put('shipping/settings', [ShippingAdminController::class, 'updateSettings'])->name('shipping.settings');
Route::post('shipping/rates', [ShippingAdminController::class, 'store'])->name('shipping.store');
Route::put('shipping/rates/{shippingRate}', [ShippingAdminController::class, 'update'])->name('shipping.update');
Route::delete('shipping/rates/{shippingRate}', [ShippingAdminController::class, 'destroy'])->name('shipping.destroy');

// Bank transfer settings
Route::get('bank-transfer', [BankTransferAdminController::class, 'index'])->name('bank-transfer.index');
Route::put('bank-transfer/settings', [BankTransferAdminController::class, 'updateSettings'])->name('bank-transfer.settings');

// Pasarela de pago (Stripe) settings
Route::get('pasarela-pago', [\App\Http\Controllers\Admin\PaymentSettingsAdminController::class, 'index'])->name('payments.index');
Route::put('pasarela-pago', [\App\Http\Controllers\Admin\PaymentSettingsAdminController::class, 'update'])->name('payments.update');

// Infographics CRUD
Route::resource('infographics', InfographicAdminController::class)->except(['show']);

// Hero settings
Route::get('hero', [AdminHeroController::class, 'edit'])->name('hero.edit');
Route::put('hero', [AdminHeroController::class, 'update'])->name('hero.update');

// Home page sections editor
Route::get('pages/home', [AdminHomePageController::class, 'edit'])->name('pages.home.edit');
Route::put('pages/home', [AdminHomePageController::class, 'update'])->name('pages.home.update');

// Lentes (catalog) page editor
Route::get('pages/lentes', [AdminLentesPageController::class, 'edit'])->name('pages.lentes.edit');
Route::put('pages/lentes', [AdminLentesPageController::class, 'update'])->name('pages.lentes.update');

// Blog page editor
Route::get('pages/blog', [AdminBlogPageController::class, 'edit'])->name('pages.blog.edit');
Route::put('pages/blog', [AdminBlogPageController::class, 'update'])->name('pages.blog.update');

// Blue light page editor
Route::get('pages/blue-light', [AdminBlueLightPageController::class, 'edit'])->name('pages.blue-light.edit');
Route::put('pages/blue-light', [AdminBlueLightPageController::class, 'update'])->name('pages.blue-light.update');

// Contact page editor
Route::get('pages/contact', [AdminContactPageController::class, 'edit'])->name('pages.contact.edit');
Route::put('pages/contact', [AdminContactPageController::class, 'update'])->name('pages.contact.update');

// Shipping & returns page editor
Route::get('pages/shipping-returns', [AdminShippingReturnsPageController::class, 'edit'])->name('pages.shipping-returns.edit');
Route::put('pages/shipping-returns', [AdminShippingReturnsPageController::class, 'update'])->name('pages.shipping-returns.update');

// Quiz page editor
Route::get('pages/quiz', [AdminQuizPageController::class, 'edit'])->name('pages.quiz.edit');
Route::put('pages/quiz', [AdminQuizPageController::class, 'update'])->name('pages.quiz.update');

// Testimonials CRUD
Route::resource('testimonials', TestimonialAdminController::class)->except(['show']);

// Customers (visor)
Route::get('customers', [\App\Http\Controllers\Admin\CustomerAdminController::class, 'index'])->name('customers.index');
Route::get('customers/{customer}', [\App\Http\Controllers\Admin\CustomerAdminController::class, 'show'])->name('customers.show');

// Stock notifications (avisos de "avísame cuando vuelva")
Route::get('stock-notifications', [\App\Http\Controllers\Admin\StockNotificationAdminController::class, 'index'])->name('stock-notifications.index');
Route::delete('stock-notifications/{stockNotification}', [\App\Http\Controllers\Admin\StockNotificationAdminController::class, 'destroy'])->name('stock-notifications.destroy');

// SEO settings
Route::get('seo', [AdminSeoController::class, 'index'])->name('seo.index');
Route::get('seo/{pageKey}', [AdminSeoController::class, 'edit'])->name('seo.edit');
Route::put('seo/{pageKey}', [AdminSeoController::class, 'update'])->name('seo.update');
