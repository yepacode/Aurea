<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;

/**
 * Importa el costo del distribuidor (cost_price) desde un CSV con columnas
 * internal_code,cost. El CSV NO se versiona (dato sensible, repo público):
 * se sube a storage/app/ en el servidor y se corre este comando.
 *
 *   php artisan ml:import-costs            # lee storage/app/ml_costs.csv
 *   php artisan ml:import-costs ruta.csv
 */
class MlImportCosts extends Command
{
    protected $signature = 'ml:import-costs {path=ml_costs.csv : CSV en storage/app o ruta absoluta}';

    protected $description = 'Importa el costo (cost_price) de los productos desde un CSV (internal_code,cost)';

    public function handle(): int
    {
        $path = $this->argument('path');
        $full = is_file($path) ? $path : storage_path('app/'.$path);
        if (! is_file($full)) {
            $this->error("No existe el archivo: {$full}");

            return self::FAILURE;
        }

        $fh = fopen($full, 'r');
        $header = fgetcsv($fh);
        if (! $header) {
            $this->error('CSV vacío.');

            return self::FAILURE;
        }
        $idx = array_flip(array_map(fn ($h) => strtolower(trim((string) $h)), $header));
        if (! isset($idx['internal_code'], $idx['cost'])) {
            $this->error('El CSV debe tener columnas: internal_code,cost');

            return self::FAILURE;
        }

        $ok = 0;
        $bad = 0;
        $miss = [];
        while (($row = fgetcsv($fh)) !== false) {
            $code = trim((string) ($row[$idx['internal_code']] ?? ''));
            $cost = (int) round((float) str_replace([',', '$', ' '], '', (string) ($row[$idx['cost']] ?? '')));
            if ($code === '' || $cost <= 0) {
                $bad++;

                continue;
            }
            $n = Product::where('internal_code', $code)->update(['cost_price' => $cost]);
            if ($n > 0) {
                $ok += $n;
            } else {
                $miss[] = $code;
            }
        }
        fclose($fh);

        $this->info("Costos actualizados: {$ok}");
        if ($bad) {
            $this->warn("Filas inválidas: {$bad}");
        }
        if ($miss) {
            $this->warn('Sin coincidencia ('.count($miss).'): '.implode(', ', array_slice($miss, 0, 30)).(count($miss) > 30 ? '…' : ''));
        }

        return self::SUCCESS;
    }
}
