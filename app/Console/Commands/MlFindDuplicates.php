<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Services\MercadoLibreService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

/**
 * Detecta publicaciones en ML que NO gestiona la API (las viejas de la planilla),
 * típicamente duplicados de las publicadas por API. Por defecto solo reporta.
 *
 *   php artisan ml:find-duplicates           # solo reporta
 *   php artisan ml:find-duplicates --pause   # las pausa (reversible)
 *   php artisan ml:find-duplicates --close    # las cierra (NO reversible)
 */
class MlFindDuplicates extends Command
{
    protected $signature = 'ml:find-duplicates {--pause : Pausa las no rastreadas} {--close : Cierra las no rastreadas (NO reversible)}';

    protected $description = 'Encuentra publicaciones de ML que no gestiona la API (duplicados de planilla)';

    public function handle(MercadoLibreService $ml): int
    {
        if (! $ml->connected()) {
            $this->error('No estás conectado a ML. Autoriza en https://bellezaaurea.com/ml/conectar');

            return self::FAILURE;
        }

        $token = $ml->accessToken();
        $userId = \App\Models\MercadoLibreSetting::get('ml_user_id');
        if (! $userId) {
            $me = Http::withToken($token)->get('https://api.mercadolibre.com/users/me');
            $userId = $me->successful() ? $me->json('id') : null;
        }
        if (! $userId) {
            $this->error('No se pudo obtener el usuario de ML.');

            return self::FAILURE;
        }

        // IDs que SÍ gestiona la API (nuestra base de datos).
        $tracked = Product::whereNotNull('ml_item_id')->pluck('ml_item_id')->flip();

        // Todas las publicaciones del vendedor (por estado, paginando).
        $all = [];
        foreach (['active', 'paused', 'under_review', 'inactive'] as $status) {
            $offset = 0;
            do {
                $res = Http::withToken($token)->get("https://api.mercadolibre.com/users/{$userId}/items/search", [
                    'status' => $status,
                    'limit'  => 50,
                    'offset' => $offset,
                ]);
                if (! $res->successful()) {
                    break;
                }
                $ids = $res->json('results', []);
                foreach ($ids as $id) {
                    $all[$id] = true;
                }
                $total = (int) $res->json('paging.total');
                $offset += 50;
            } while ($offset < $total && $offset < 2000);
        }

        // No rastreadas = están en ML pero no en nuestra base de datos.
        $untracked = array_values(array_filter(array_keys($all), fn ($id) => ! isset($tracked[$id])));

        $this->info('Publicaciones en ML: '.count($all).' | gestionadas por API: '.$tracked->count());
        $this->info('NO gestionadas por la API (posibles duplicados de planilla): '.count($untracked));

        if (empty($untracked)) {
            $this->info('No hay duplicados. 🎉');

            return self::SUCCESS;
        }

        // Detalle (multiget).
        foreach (array_chunk($untracked, 20) as $chunk) {
            $res = Http::withToken($token)->get('https://api.mercadolibre.com/items', [
                'ids'        => implode(',', $chunk),
                'attributes' => 'id,title,status,price',
            ]);
            foreach ($res->json() ?? [] as $row) {
                $b = $row['body'] ?? [];
                $this->line('  '.($b['id'] ?? '?').'  ['.($b['status'] ?? '?').']  $'.number_format((float) ($b['price'] ?? 0), 0, ',', '.').'  '.Str::limit($b['title'] ?? '', 45));
            }
            usleep(150000);
        }

        if (! $this->option('pause') && ! $this->option('close')) {
            $this->newLine();
            $this->warn('Modo reporte. Para pausarlas: --pause  |  para cerrarlas: --close');

            return self::SUCCESS;
        }

        $accion = $this->option('close') ? 'closed' : 'paused';
        $this->newLine();
        $this->info("Aplicando estado '{$accion}' a ".count($untracked).' publicaciones...');
        $ok = 0;
        $fail = 0;
        foreach ($untracked as $id) {
            $res = Http::withToken($token)->put("https://api.mercadolibre.com/items/{$id}", ['status' => $accion]);
            if ($res->successful()) {
                $ok++;
            } else {
                $fail++;
                $this->warn("✗ {$id}: ".Str::limit($res->body(), 120));
            }
            usleep(300000);
        }
        $this->newLine();
        $this->info("Listo. {$accion}: {$ok} | Fallidos: {$fail}");

        return self::SUCCESS;
    }
}
