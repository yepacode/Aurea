<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Services\MercadoLibreService;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

/**
 * Publica productos del catálogo en Mercado Libre vía API.
 *
 *   php artisan ml:publish --dry-run        # muestra categoría/precio sin publicar
 *   php artisan ml:publish --id=123         # publica solo ese producto (prueba)
 *   php artisan ml:publish --limit=10       # publica hasta 10
 *   php artisan ml:publish                  # publica todos los activos sin ml_item_id
 */
class MlPublish extends Command
{
    protected $signature = 'ml:publish {--dry-run : Muestra categoría y precio sin publicar} {--limit= : Máximo de productos} {--id= : Publica solo este producto (id)}';

    protected $description = 'Publica productos del catálogo en Mercado Libre vía API';

    public function handle(MercadoLibreService $ml): int
    {
        if (! $ml->configured()) {
            $this->error('Faltan credenciales de ML en .env (MERCADOLIBRE_APP_ID / MERCADOLIBRE_SECRET).');

            return self::FAILURE;
        }

        if (! $this->option('dry-run') && ! $ml->connected()) {
            $this->error('No estás conectado a ML. Autoriza en https://bellezaaurea.com/ml/conectar');

            return self::FAILURE;
        }

        if ($id = $this->option('id')) {
            $products = Product::where('id', $id)->get();
        } else {
            $query = Product::query()->where('is_active', true)->whereNull('ml_item_id');
            if ($limit = $this->option('limit')) {
                $query->limit((int) $limit);
            }
            $products = $query->get();
        }

        $this->info("Productos a procesar: {$products->count()}");
        $ok = 0;
        $fail = 0;

        foreach ($products as $p) {
            if ($this->option('dry-run')) {
                $cat = $ml->predictCategory($p->name);
                $this->line("[DRY] {$p->name}  ->  cat {$cat}  ->  \$".number_format($ml->mlPrice((float) $p->price), 0, ',', '.'));

                continue;
            }

            [$success, $info] = $ml->publish($p);
            if ($success) {
                $ok++;
                $this->info("✓ {$p->name}  ->  {$info}");
            } else {
                $fail++;
                $this->warn("✗ {$p->name}: ".Str::limit($info, 180));
            }
            usleep(400000); // ~0.4s entre llamadas para no saturar la API
        }

        $this->newLine();
        $this->info("Listo. Publicados: {$ok} | Fallidos: {$fail}");

        return self::SUCCESS;
    }
}
