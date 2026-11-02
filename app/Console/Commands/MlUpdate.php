<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Services\MercadoLibreService;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

/**
 * Actualiza publicaciones YA creadas en Mercado Libre para mejorar su calidad
 * (más atributos / ficha técnica + envío gratis según umbral de precio).
 *
 *   php artisan ml:update --dry-run       # muestra cuáles tendrían envío gratis
 *   php artisan ml:update --id=123         # actualiza solo ese producto (prueba)
 *   php artisan ml:update --limit=20       # actualiza hasta 20
 *   php artisan ml:update                  # actualiza todos los publicados
 */
class MlUpdate extends Command
{
    protected $signature = 'ml:update {--dry-run : Muestra el plan sin actualizar} {--limit= : Máximo de productos} {--id= : Actualiza solo este producto (id)}';

    protected $description = 'Actualiza publicaciones existentes en ML (atributos + envío) para mejorar la calidad';

    public function handle(MercadoLibreService $ml): int
    {
        if (! $ml->connected()) {
            $this->error('No estás conectado a ML. Autoriza en https://bellezaaurea.com/ml/conectar');

            return self::FAILURE;
        }

        if ($id = $this->option('id')) {
            $products = Product::where('id', $id)->whereNotNull('ml_item_id')->get();
        } else {
            $query = Product::query()->whereNotNull('ml_item_id');
            if ($limit = $this->option('limit')) {
                $query->limit((int) $limit);
            }
            $products = $query->get();
        }

        $min = (int) (config('services.mercadolibre.free_shipping_min') ?? 0);
        $this->info("Publicaciones a actualizar: {$products->count()}");
        $this->line('Umbral de envío gratis: '.($min > 0 ? '$'.number_format($min, 0, ',', '.') : 'desactivado'));
        $this->newLine();

        $ok = 0;
        $fail = 0;
        foreach ($products as $p) {
            if ($this->option('dry-run')) {
                $cost = $p->cost_price !== null ? (float) $p->cost_price : null;
                $price = $ml->mlPrice((float) $p->price, $cost);
                $gratis = ($min > 0 && $price >= $min) ? '  [ENVÍO GRATIS]' : '';
                $this->line('[DRY] $'.number_format($price, 0, ',', '.').'  '.Str::limit($p->name, 50).$gratis);

                continue;
            }

            [$success, $info] = $ml->updateItem($p);
            if ($success) {
                $ok++;
                $this->info("✓ {$p->name}");
            } else {
                $fail++;
                $this->warn("✗ {$p->name}: ".Str::limit($info, 200));
            }
            usleep(400000); // ~0.4s entre llamadas
        }

        $this->newLine();
        $this->info("Listo. Actualizados: {$ok} | Fallidos: {$fail}");

        return self::SUCCESS;
    }
}
