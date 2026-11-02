<?php

namespace App\Console\Commands;

use App\Models\MercadoLibreSetting;
use App\Services\MercadoLibreService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

/**
 * Lista las publicaciones de ML con problemas y su motivo (sub_status).
 *
 *   php artisan ml:issues                       # inactivas (incumplen políticas)
 *   php artisan ml:issues --status=paused
 *   php artisan ml:issues --status=payment_required
 *   php artisan ml:issues --status=active       # revisar sub_status de las activas
 */
class MlIssues extends Command
{
    protected $signature = 'ml:issues {--status=inactive : Estado a inspeccionar} {--limit=300 : Máximo de publicaciones}';

    protected $description = 'Lista las publicaciones de ML con problemas y su motivo (sub_status)';

    public function handle(MercadoLibreService $ml): int
    {
        if (! $ml->connected()) {
            $this->error('No conectado a ML. Autoriza en /ml/conectar.');

            return self::FAILURE;
        }

        $token = $ml->accessToken();
        $userId = MercadoLibreSetting::get('ml_user_id');
        if (! $userId) {
            $me = Http::withToken($token)->get('https://api.mercadolibre.com/users/me');
            $userId = $me->successful() ? $me->json('id') : null;
        }
        if (! $userId) {
            $this->error('No se pudo obtener el usuario de ML.');

            return self::FAILURE;
        }

        $status = (string) $this->option('status');
        $max    = (int) $this->option('limit');

        // 1) Recoger los IDs del estado pedido (paginando).
        $ids = [];
        $offset = 0;
        do {
            $res = Http::withToken($token)->get("https://api.mercadolibre.com/users/{$userId}/items/search", [
                'status' => $status,
                'limit'  => 50,
                'offset' => $offset,
            ]);
            if (! $res->successful()) {
                $this->error('Error consultando ML: '.$res->status().' '.$res->body());
                break;
            }
            $ids = array_merge($ids, $res->json('results', []));
            $total = (int) $res->json('paging.total');
            $offset += 50;
        } while ($offset < $total && count($ids) < $max);

        $this->info("Publicaciones en estado '{$status}': ".count($ids));
        if (empty($ids)) {
            return self::SUCCESS;
        }

        // 2) Traer detalle (multiget de a 20) y agrupar por motivo.
        $reasons = [];
        foreach (array_chunk($ids, 20) as $chunk) {
            $res = Http::withToken($token)->get('https://api.mercadolibre.com/items', [
                'ids'        => implode(',', $chunk),
                'attributes' => 'id,title,status,sub_status,health',
            ]);
            foreach ($res->json() ?? [] as $row) {
                $b = $row['body'] ?? [];
                $sub = ! empty($b['sub_status']) ? implode('|', (array) $b['sub_status']) : '(sin sub_status)';
                $reasons[$sub] = ($reasons[$sub] ?? 0) + 1;
                $this->line("  {$b['id']}  [{$sub}]  ".Str::limit($b['title'] ?? '', 55));
            }
            usleep(200000);
        }

        $this->newLine();
        $this->info('RESUMEN por motivo (sub_status):');
        arsort($reasons);
        foreach ($reasons as $motivo => $n) {
            $this->line("  {$n}  ·  {$motivo}");
        }

        return self::SUCCESS;
    }
}
