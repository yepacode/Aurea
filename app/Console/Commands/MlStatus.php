<?php

namespace App\Console\Commands;

use App\Models\MercadoLibreSetting;
use App\Models\Product;
use App\Services\MercadoLibreService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

/**
 * Verifica cuántos productos están publicados en Mercado Libre.
 *
 *   php artisan ml:status            # resumen (BD + estados en ML)
 *   php artisan ml:status --list     # además lista los activos sin publicar
 */
class MlStatus extends Command
{
    protected $signature = 'ml:status {--list : Lista los productos activos que aún no están en ML}';

    protected $description = 'Verifica cuántos productos están publicados en Mercado Libre y su estado';

    public function handle(MercadoLibreService $ml): int
    {
        $activos = Product::where('is_active', true)->count();
        $conItem = Product::where('is_active', true)->whereNotNull('ml_item_id')->count();
        $faltan  = Product::where('is_active', true)->whereNull('ml_item_id')->count();

        $this->info('BASE DE DATOS:');
        $this->line("  Productos activos:        {$activos}");
        $this->line("  Con publicación (ml_item): {$conItem}");
        $this->line("  Faltan por publicar:       {$faltan}");

        if ($this->option('list') && $faltan) {
            $this->newLine();
            $this->warn('Productos activos SIN publicar:');
            Product::where('is_active', true)->whereNull('ml_item_id')
                ->orderBy('id')->get(['id', 'name'])
                ->each(fn ($p) => $this->line("  {$p->id}  {$p->name}"));
        }

        // Estado real en Mercado Libre (requiere token).
        $token = $ml->connected() ? $ml->accessToken() : null;
        if (! $token) {
            $this->newLine();
            $this->warn('Sin conexión a ML para consultar estados. Autoriza en /ml/conectar.');

            return self::SUCCESS;
        }

        $userId = MercadoLibreSetting::get('ml_user_id');
        if (! $userId) {
            $me = Http::withToken($token)->get('https://api.mercadolibre.com/users/me');
            if ($me->successful()) {
                $userId = $me->json('id');
                MercadoLibreSetting::set('ml_user_id', $userId);
            }
        }

        if (! $userId) {
            $this->warn('No se pudo obtener el usuario de ML.');

            return self::SUCCESS;
        }

        $this->newLine();
        $this->info("ESTADO EN MERCADO LIBRE (cuenta {$userId}):");
        $estados = [
            'active'           => 'Activas',
            'paused'           => 'Pausadas',
            'under_review'     => 'En revisión',
            'inactive'         => 'Inactivas',
            'payment_required' => 'Requieren pago',
            'closed'           => 'Cerradas',
        ];
        $totalML = 0;
        foreach ($estados as $status => $label) {
            $res = Http::withToken($token)->get("https://api.mercadolibre.com/users/{$userId}/items/search", [
                'status' => $status,
                'limit'  => 1,
            ]);
            $total = $res->successful() ? (int) $res->json('paging.total') : null;
            if ($total !== null) {
                $totalML += $total;
            }
            $this->line("  {$label}: ".($total ?? '¿?'));
        }
        $this->line("  ----");
        $this->line("  Total en ML: {$totalML}");

        if ($faltan > 0) {
            $this->newLine();
            $this->warn("Quedan {$faltan} activos sin publicar. Reintenta con: php artisan ml:publish");
        }

        return self::SUCCESS;
    }
}
