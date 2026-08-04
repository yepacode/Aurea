<?php

namespace App\Console\Commands;

use App\Mail\ReviewRequest;
use App\Models\Order;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class RequestReviews extends Command
{
    protected $signature = 'orders:request-reviews {--days=3 : Días tras la entrega para pedir la reseña}';

    protected $description = 'Envía correos post-compra pidiendo reseña a pedidos entregados.';

    public function handle(): int
    {
        $days = (int) $this->option('days');
        $cutoff = now()->subDays($days);

        $orders = Order::query()
            ->where('status', 'delivered')
            ->whereNull('review_requested_at')
            ->where('updated_at', '<=', $cutoff)
            ->with(['customer', 'items.product'])
            ->get();

        $sent = 0;

        foreach ($orders as $order) {
            $email = $order->customer?->email;
            if (! $email) {
                continue;
            }

            try {
                Mail::to($email)->send(new ReviewRequest($order));
                $order->update(['review_requested_at' => now()]);
                $sent++;
            } catch (\Throwable $e) {
                report($e);
            }
        }

        $this->info("Solicitudes de reseña enviadas: {$sent}");

        return self::SUCCESS;
    }
}
