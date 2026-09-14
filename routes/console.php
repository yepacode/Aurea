<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Post-compra: pedir reseña a pedidos entregados hace ≥3 días (una sola vez).
Schedule::command('orders:request-reviews')->dailyAt('10:00');

// Carrito abandonado: recordatorio 1h+ después de la última actividad (una sola vez).
Schedule::command('carts:remind-abandoned')->hourly();

// NPS post-compra: pide feedback 15-20 días después del pago (una sola vez por pedido).
Schedule::command('nps:send')->dailyAt('11:00');
