<?php

namespace App\Console\Commands;

use App\Mail\NpsRequest;
use App\Models\NpsResponse;
use App\Models\Order;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class SendNpsRequests extends Command
{
    protected $signature = 'nps:send
        {--min-days=15 : Días mínimos desde el pago}
        {--max-days=20 : Días máximos desde el pago}
        {--dry-run : No crea NpsResponse ni envía correos, solo lista los candidatos}';

    protected $description = 'Envía la encuesta NPS a pedidos pagados hace 15-20 días que aún no la han recibido.';

    public function handle(): int
    {
        $minDays = (int) $this->option('min-days');
        $maxDays = (int) $this->option('max-days');
        $dryRun  = (bool) $this->option('dry-run');

        // Ventana: pedidos pagados hace entre max-days y min-days
        // (updated_at es cuando el pedido pasó a paid; si tu tienda tuviera un
        // campo paid_at, ese sería el ideal; por compatibilidad usamos updated_at.)
        $to   = now()->subDays($minDays)->endOfDay();
        $from = now()->subDays($maxDays)->startOfDay();

        $orders = Order::query()
            ->where('payment_status', 'paid')
            ->whereBetween('updated_at', [$from, $to])
            ->whereNotIn('id', function ($q) {
                $q->from('nps_responses')->select('order_id');
            })
            ->with('customer')
            ->get();

        $this->info("Ventana: {$from->toDateString()} → {$to->toDateString()}");
        $this->info("Candidatos encontrados: {$orders->count()}");

        $sent = 0;
        $skipped = 0;

        foreach ($orders as $order) {
            $email = $order->customer?->email;
            if (! $email) {
                $skipped++;
                $this->warn("  ↷ Pedido #{$order->id}: cliente sin email, se omite.");
                continue;
            }

            if ($dryRun) {
                $this->line("  ✓ [dry-run] Pedido #{$order->id} → {$email}");
                $sent++;
                continue;
            }

            $nps = NpsResponse::create([
                'order_id'    => $order->id,
                'customer_id' => $order->customer_id,
                'token'       => Str::random(40),
                'sent_at'     => now(),
            ]);

            try {
                Mail::to($email)->send(new NpsRequest($nps));
                $sent++;
                $this->line("  ✓ Pedido #{$order->id} → {$email}");
            } catch (\Throwable $e) {
                report($e);
                $this->error("  ✗ Pedido #{$order->id}: {$e->getMessage()}");
                // Deshacemos el registro si falla el envío para que se reintente mañana.
                $nps->delete();
                $skipped++;
            }
        }

        $tag = $dryRun ? '[dry-run] ' : '';
        $this->info("{$tag}Encuestas NPS enviadas: {$sent} · omitidas: {$skipped}");

        return self::SUCCESS;
    }
}
