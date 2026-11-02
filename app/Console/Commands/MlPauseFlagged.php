<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Services\MercadoLibreService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

/**
 * Reporta (y opcionalmente pausa) las publicaciones de ML que están marcadas
 * por moderación — típicamente las que tienen el logo de placeholder en vez de
 * foto real ("el título y/o las fotos no coinciden").
 *
 *   php artisan ml:pause-flagged            # SOLO reporta, no toca nada
 *   php artisan ml:pause-flagged --apply    # pausa las marcadas (reversible)
 *   php artisan ml:pause-flagged --apply --close   # las cierra (NO reversible)
 */
class MlPauseFlagged extends Command
{
    protected $signature = 'ml:pause-flagged {--apply : Aplica la acción (sin esto solo reporta)} {--close : Cierra en vez de pausar (NO reversible)}';

    protected $description = 'Reporta y opcionalmente pausa las publicaciones de ML marcadas por moderación (foto/título)';

    public function handle(MercadoLibreService $ml): int
    {
        if (! $ml->connected()) {
            $this->error('No estás conectado a ML. Autoriza en https://bellezaaurea.com/ml/conectar');

            return self::FAILURE;
        }

        $token = $ml->accessToken();

        $items = Product::query()
            ->whereNotNull('ml_item_id')
            ->get(['id', 'name', 'ml_item_id'])
            ->keyBy('ml_item_id');

        $this->info('Revisando '.$items->count().' publicaciones en ML...');

        // Estados que consideramos "marcados" (activables/pausables) vs cerrados.
        $flagged = [];   // [item_id => ['p'=>Product, 'status'=>..., 'sub'=>...]]
        $closed  = [];
        $resumen = [];

        foreach (array_chunk($items->keys()->all(), 20) as $chunk) {
            $res = Http::withToken($token)->get('https://api.mercadolibre.com/items', [
                'ids'        => implode(',', $chunk),
                'attributes' => 'id,status,sub_status',
            ]);
            foreach ($res->json() ?? [] as $row) {
                $b = $row['body'] ?? [];
                $itemId = $b['id'] ?? null;
                $status = $b['status'] ?? '';
                $sub    = (array) ($b['sub_status'] ?? []);
                if (! $itemId || ! isset($items[$itemId])) {
                    continue;
                }
                $esProblema = in_array($status, ['under_review', 'inactive'], true) || ! empty($sub);
                if (! $esProblema) {
                    continue;
                }

                $clave = $status.($sub ? ' / '.implode('|', $sub) : '');
                $resumen[$clave] = ($resumen[$clave] ?? 0) + 1;

                $registro = ['p' => $items[$itemId], 'status' => $status, 'sub' => implode('|', $sub)];
                if ($status === 'closed') {
                    $closed[$itemId] = $registro;
                } else {
                    $flagged[$itemId] = $registro;
                }
            }
            usleep(150000);
        }

        $this->newLine();
        $this->info('RESUMEN por estado/motivo:');
        arsort($resumen);
        foreach ($resumen as $clave => $n) {
            $this->line("  {$n}  ·  {$clave}");
        }
        $this->newLine();
        $this->line('Marcadas pausables/activables: '.count($flagged).'  |  ya cerradas: '.count($closed));

        // Listado (para saber cuáles necesitan foto real).
        $this->newLine();
        $this->info('PUBLICACIONES MARCADAS (necesitan foto real o revisión):');
        foreach ($flagged as $itemId => $r) {
            $this->line("  {$r['p']->id}  {$itemId}  [{$r['status']}{$r['sub']}]  ".Str::limit($r['p']->name, 45));
        }

        if (! $this->option('apply')) {
            $this->newLine();
            $this->warn('Modo reporte (no se tocó nada). Para pausarlas: php artisan ml:pause-flagged --apply');

            return self::SUCCESS;
        }

        // Aplicar: pausar (o cerrar) las marcadas.
        $accion = $this->option('close') ? 'closed' : 'paused';
        $this->newLine();
        $this->info("Aplicando estado '{$accion}' a ".count($flagged).' publicaciones...');
        $ok = 0;
        $fail = 0;
        foreach ($flagged as $itemId => $r) {
            $res = Http::withToken($token)->put("https://api.mercadolibre.com/items/{$itemId}", [
                'status' => $accion,
            ]);
            if ($res->successful()) {
                $ok++;
                $this->info("✓ {$r['p']->name}");
            } else {
                $fail++;
                $this->warn("✗ {$r['p']->name}: ".Str::limit($res->body(), 160));
            }
            usleep(300000);
        }

        $this->newLine();
        $this->info("Listo. {$accion}: {$ok} | Fallidos: {$fail}");

        return self::SUCCESS;
    }
}
