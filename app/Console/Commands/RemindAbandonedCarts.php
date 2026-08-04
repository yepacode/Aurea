<?php

namespace App\Console\Commands;

use App\Mail\AbandonedCartReminder;
use App\Models\AbandonedCart;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class RemindAbandonedCarts extends Command
{
    protected $signature = 'carts:remind-abandoned
        {--after=1 : Horas de inactividad para considerar abandonado}
        {--before=72 : No recordar carritos más viejos que estas horas}';

    protected $description = 'Envía recordatorios de carrito abandonado a clientes con cuenta.';

    public function handle(): int
    {
        $after = (int) $this->option('after');
        $before = (int) $this->option('before');

        $carts = AbandonedCart::query()
            ->whereNull('reminded_at')
            ->where('updated_at', '<=', now()->subHours($after))
            ->where('updated_at', '>=', now()->subHours($before))
            ->with('customer')
            ->get();

        $sent = 0;

        foreach ($carts as $cart) {
            $email = $cart->customer?->email;
            if (! $email || empty($cart->items)) {
                continue;
            }

            try {
                Mail::to($email)->send(new AbandonedCartReminder($cart));
                $cart->update(['reminded_at' => now()]);
                $sent++;
            } catch (\Throwable $e) {
                report($e);
            }
        }

        $this->info("Recordatorios de carrito abandonado enviados: {$sent}");

        return self::SUCCESS;
    }
}
