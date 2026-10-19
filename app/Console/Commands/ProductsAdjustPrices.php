<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

/**
 * Ajuste competitivo de precios contra el catálogo de Miracle.
 *
 * Regla: ubicar el precio un % por debajo del público de Miracle
 * (centro_experiencia), PERO sin bajar nunca de un piso de margen
 * sobre el costo (distribuidor). Si el precio resultante no es menor
 * al actual, el producto NO se toca — este comando sólo baja precios.
 *
 * Uso:
 *   php artisan products:adjust-prices --dry-run
 *   php artisan products:adjust-prices --descuento=10 --piso=25
 *   php artisan products:adjust-prices --csv=ajuste.csv --dry-run
 */
class ProductsAdjustPrices extends Command
{
    protected $signature = 'products:adjust-prices
        {--dry-run : simular sin guardar}
        {--descuento=10 : % por debajo del precio público de Miracle}
        {--piso=25 : margen mínimo garantizado sobre el costo (%)}
        {--redondeo=100 : redondear el precio final a múltiplos de N}
        {--csv= : exportar el detalle a este archivo CSV}';

    protected $description = 'Baja precios para quedar bajo el público de Miracle sin romper un piso de margen. Nunca sube precios.';

    public function handle(): int
    {
        $dry       = (bool) $this->option('dry-run');
        $descuento = max(0, (float) $this->option('descuento'));
        $piso      = max(0, (float) $this->option('piso'));
        $redondeo  = max(1, (int) $this->option('redondeo'));

        $catalogo = $this->traerCatalogoMiracle();
        if ($catalogo === null) {
            return self::FAILURE;
        }

        $this->info(sprintf(
            '%sRegla: %s%% bajo el público de Miracle · piso de margen %s%% · redondeo a %s',
            $dry ? '[DRY-RUN] ' : '',
            $descuento,
            $piso,
            number_format($redondeo, 0, ',', '.')
        ));
        $this->newLine();

        $bajan = [];
        $sinTocar = 0;
        $sinMatch = 0;

        Product::query()
            ->where('is_active', 1)
            ->whereNotNull('internal_code')
            ->orderBy('id')
            ->chunkById(200, function ($chunk) use ($catalogo, $descuento, $piso, $redondeo, &$bajan, &$sinTocar, &$sinMatch) {
                foreach ($chunk as $p) {
                    $ref = mb_strtolower(trim((string) $p->internal_code));
                    $m = $catalogo[$ref] ?? null;
                    if (! $m) {
                        $sinMatch++;
                        continue;
                    }

                    $costo    = (float) ($m['precios']['distribuidor'] ?? 0);
                    $publico  = (float) ($m['precios']['centro_experiencia'] ?? 0);
                    $actual   = (float) $p->price;

                    if ($costo <= 0 || $publico <= 0) {
                        $sinTocar++;
                        continue;
                    }

                    $objetivo = $publico * (1 - $descuento / 100);
                    $minimo   = $costo * (1 + $piso / 100);
                    $nuevo    = (float) (round(max($objetivo, $minimo) / $redondeo) * $redondeo);

                    // El redondeo hacia abajo puede dejar el precio bajo el piso de
                    // margen; en ese caso subimos al siguiente múltiplo.
                    if ($nuevo < $minimo) {
                        $nuevo = (float) (ceil($minimo / $redondeo) * $redondeo);
                    }

                    // Este comando SÓLO baja. Si no mejora el precio, se deja igual.
                    if ($nuevo >= $actual) {
                        $sinTocar++;
                        continue;
                    }

                    $bajan[] = [
                        'id'      => $p->id,
                        'codigo'  => $p->internal_code,
                        'nombre'  => $p->name,
                        'actual'  => $actual,
                        'nuevo'   => $nuevo,
                        'baja'    => $actual - $nuevo,
                        'costo'   => $costo,
                        'publico' => $publico,
                        'margen'  => ($nuevo - $costo) / $costo * 100,
                        'stock'   => (int) $p->stock,
                        'modelo'  => $p,
                    ];
                }
            });

        if (empty($bajan)) {
            $this->warn('Ningún producto cumple la regla para bajar de precio.');
            return self::SUCCESS;
        }

        // Guard: con la regla aplicada nadie debería quedar bajo el piso.
        $bajoElPiso = array_filter($bajan, fn ($b) => $b['margen'] < $piso - 0.5);
        if (! empty($bajoElPiso)) {
            $this->error('Abortado: ' . count($bajoElPiso) . ' producto(s) quedarían bajo el piso de margen.');
            return self::FAILURE;
        }

        $this->mostrarMuestra($bajan);
        $this->mostrarResumen($bajan, $sinTocar, $sinMatch, $piso);

        if ($ruta = $this->option('csv')) {
            $this->exportarCsv($bajan, $ruta);
        }

        if ($dry) {
            $this->newLine();
            $this->comment('DRY-RUN: no se guardó nada. Quita --dry-run para aplicar.');
            return self::SUCCESS;
        }

        foreach ($bajan as $b) {
            Product::whereKey($b['id'])->update(['price' => $b['nuevo']]);
        }

        $this->newLine();
        $this->info('Precios actualizados: ' . count($bajan));

        return self::SUCCESS;
    }

    /** Descarga el catálogo de Miracle indexado por referencia en minúsculas. */
    private function traerCatalogoMiracle(): ?array
    {
        $url   = (string) config('miracle.api_url');
        $token = (string) config('miracle.api_token');

        if ($url === '' || $token === '') {
            $this->error('Falta MIRACLE_API_URL o MIRACLE_API_TOKEN en el .env.');
            return null;
        }

        $this->line('Consultando el catálogo de Miracle…');

        try {
            $res = Http::timeout((int) config('miracle.timeout', 120))
                ->acceptJson()
                ->get($url, ['token' => $token]);
        } catch (\Throwable $e) {
            $this->error('No se pudo contactar a Miracle: ' . $e->getMessage());
            return null;
        }

        if (! $res->successful()) {
            $this->error('Miracle respondió HTTP ' . $res->status());
            return null;
        }

        $data = $res->json('data');
        if (! is_array($data) || empty($data)) {
            $this->error('La respuesta de Miracle no trae productos.');
            return null;
        }

        $byRef = [];
        foreach ($data as $p) {
            $ref = mb_strtolower(trim((string) ($p['referencia'] ?? '')));
            if ($ref !== '') {
                $byRef[$ref] = $p;
            }
        }

        $this->line('Catálogo recibido: ' . count($byRef) . ' referencias.');
        $this->newLine();

        return $byRef;
    }

    private function mostrarMuestra(array $bajan): void
    {
        $muestra = $bajan;
        usort($muestra, fn ($a, $b) => $b['stock'] <=> $a['stock']);

        $this->table(
            ['Producto', 'Hoy', 'Nuevo', 'Baja', 'Costo', 'Margen', 'Stock'],
            array_map(fn ($b) => [
                mb_substr($b['nombre'], 0, 38),
                '$' . number_format($b['actual'], 0, ',', '.'),
                '$' . number_format($b['nuevo'], 0, ',', '.'),
                '-$' . number_format($b['baja'], 0, ',', '.'),
                '$' . number_format($b['costo'], 0, ',', '.'),
                round($b['margen'], 1) . '%',
                number_format($b['stock'], 0, ',', '.'),
            ], array_slice($muestra, 0, 20))
        );
    }

    private function mostrarResumen(array $bajan, int $sinTocar, int $sinMatch, float $piso): void
    {
        $margenes = array_column($bajan, 'margen');
        sort($margenes);
        $n = count($margenes);
        $bajas = array_column($bajan, 'baja');

        $this->info('── Resumen ──');
        $this->line('  Productos que bajan:     ' . $n);
        $this->line('  Sin tocar:               ' . $sinTocar . ' (no se puede bajar sin romper el piso)');
        $this->line('  Sin match en Miracle:    ' . $sinMatch);
        $this->line('  Rebaja promedio:         $' . number_format(array_sum($bajas) / max(1, $n), 0, ',', '.'));
        $this->line('  Rebaja máxima:           $' . number_format(max($bajas), 0, ',', '.'));
        $this->line('  Margen mínimo resultante:' . round($margenes[0], 1) . '%  (piso pedido: ' . $piso . '%)');
        $this->line('  Margen mediano:          ' . round($margenes[(int) ($n * 0.5)], 1) . '%');
        $this->line('  Productos en pérdida:    ' . count(array_filter($margenes, fn ($m) => $m < 0)));
    }

    private function exportarCsv(array $bajan, string $ruta): void
    {
        $fh = fopen($ruta, 'w');
        if ($fh === false) {
            $this->error('No se pudo escribir el CSV en: ' . $ruta);
            return;
        }

        fwrite($fh, "\xEF\xBB\xBF"); // BOM para que Excel lea los acentos
        fputcsv($fh, ['ID', 'Codigo', 'Producto', 'Precio actual', 'Precio nuevo', 'Baja', 'Costo', 'Publico Miracle', 'Margen %', 'Stock']);

        foreach ($bajan as $b) {
            fputcsv($fh, [
                $b['id'],
                $b['codigo'],
                $b['nombre'],
                (int) $b['actual'],
                (int) $b['nuevo'],
                (int) $b['baja'],
                (int) $b['costo'],
                (int) $b['publico'],
                round($b['margen'], 1),
                $b['stock'],
            ]);
        }

        fclose($fh);
        $this->newLine();
        $this->info('CSV exportado en: ' . $ruta);
    }
}
