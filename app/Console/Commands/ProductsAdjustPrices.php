<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Services\MiracleCatalog;
use Illuminate\Console\Command;

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
        {--subir : modo inverso: sólo SUBE los productos cuyo margen está bajo el piso}
        {--descuento=10 : % por debajo del precio público de Miracle (modo bajar)}
        {--piso=25 : margen mínimo garantizado sobre el costo (%)}
        {--objetivo=30 : margen al que se lleva el producto en modo --subir (%)}
        {--redondeo=100 : redondear el precio final a múltiplos de N}
        {--csv= : exportar el detalle a este archivo CSV}';

    protected $description = 'Ajusta precios contra el catálogo de Miracle. Por defecto sólo baja (para quedar bajo su precio público sin romper un piso de margen); con --subir sólo corrige al alza los productos que están regalados.';

    public function handle(): int
    {
        $dry       = (bool) $this->option('dry-run');
        $subir     = (bool) $this->option('subir');
        $descuento = max(0, (float) $this->option('descuento'));
        $piso      = max(0, (float) $this->option('piso'));
        $objetivo  = max(0, (float) $this->option('objetivo'));
        $redondeo  = max(1, (int) $this->option('redondeo'));

        if ($subir && $objetivo < $piso) {
            $this->error("El margen objetivo ({$objetivo}%) no puede ser menor al piso ({$piso}%).");
            return self::FAILURE;
        }

        $catalogo = $this->traerCatalogoMiracle();
        if ($catalogo === null) {
            return self::FAILURE;
        }

        $this->info(sprintf(
            '%s%s',
            $dry ? '[DRY-RUN] ' : '',
            $subir
                ? "Modo SUBIR: productos con margen < {$piso}% se llevan a {$objetivo}% · redondeo a " . number_format($redondeo, 0, ',', '.')
                : "Modo BAJAR: {$descuento}% bajo el público de Miracle · piso de margen {$piso}% · redondeo a " . number_format($redondeo, 0, ',', '.')
        ));
        $this->newLine();

        $bajan = [];
        $sinTocar = 0;
        $sinMatch = 0;

        Product::query()
            ->where('is_active', 1)
            ->whereNotNull('internal_code')
            ->orderBy('id')
            ->chunkById(200, function ($chunk) use ($catalogo, $subir, $descuento, $piso, $objetivo, $redondeo, &$bajan, &$sinTocar, &$sinMatch) {
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

                    if ($costo <= 0) {
                        $sinTocar++;
                        continue;
                    }

                    $minimo = $costo * (1 + $piso / 100);

                    if ($subir) {
                        // Sólo tocamos los que están por debajo del piso de margen.
                        if ($actual >= $minimo) {
                            $sinTocar++;
                            continue;
                        }
                        // Se llevan al margen objetivo, redondeando hacia arriba para
                        // no quedar por debajo de él tras el redondeo.
                        $nuevo = (float) (ceil($costo * (1 + $objetivo / 100) / $redondeo) * $redondeo);

                        if ($nuevo <= $actual) {
                            $sinTocar++;
                            continue;
                        }
                    } else {
                        if ($publico <= 0) {
                            $sinTocar++;
                            continue;
                        }

                        $target = $publico * (1 - $descuento / 100);
                        $nuevo  = (float) (round(max($target, $minimo) / $redondeo) * $redondeo);

                        // El redondeo hacia abajo puede dejar el precio bajo el piso de
                        // margen; en ese caso subimos al siguiente múltiplo.
                        if ($nuevo < $minimo) {
                            $nuevo = (float) (ceil($minimo / $redondeo) * $redondeo);
                        }

                        // En modo bajar, si no mejora el precio se deja igual.
                        if ($nuevo >= $actual) {
                            $sinTocar++;
                            continue;
                        }
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
            $this->warn($subir
                ? 'Ningún producto está por debajo del piso de margen. Nada que subir.'
                : 'Ningún producto cumple la regla para bajar de precio.');
            return self::SUCCESS;
        }

        // Guard: con la regla aplicada nadie debería quedar bajo el piso.
        $bajoElPiso = array_filter($bajan, fn ($b) => $b['margen'] < $piso - 0.5);
        if (! empty($bajoElPiso)) {
            $this->error('Abortado: ' . count($bajoElPiso) . ' producto(s) quedarían bajo el piso de margen.');
            return self::FAILURE;
        }

        // Guard extra en modo subir: ningún precio puede terminar más bajo.
        if ($subir) {
            $retrocesos = array_filter($bajan, fn ($b) => $b['nuevo'] <= $b['actual']);
            if (! empty($retrocesos)) {
                $this->error('Abortado: ' . count($retrocesos) . ' producto(s) bajarían de precio en modo --subir.');
                return self::FAILURE;
            }
        }

        $this->mostrarMuestra($bajan, $subir);
        $this->mostrarResumen($bajan, $sinTocar, $sinMatch, $piso, $subir);

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

    /**
     * Descarga el catálogo de Miracle indexado por referencia en minúsculas.
     * La paginación la resuelve MiracleCatalog: el endpoint de producción
     * pagina y quedarse con la primera página daría precios a medias.
     */
    private function traerCatalogoMiracle(): ?array
    {
        $this->line('Consultando el catálogo de Miracle…');

        try {
            $byRef = app(MiracleCatalog::class)->porReferencia(
                function (int $pagina, int $enPagina, int $acumulado) {
                    $this->line("  página {$pagina}: {$enPagina} filas (acumulado {$acumulado})");
                }
            );
        } catch (\Throwable $e) {
            $this->error($e->getMessage());
            return null;
        }

        $this->line('Catálogo recibido: ' . count($byRef) . ' referencias.');
        $this->newLine();

        return $byRef;
    }

    private function mostrarMuestra(array $bajan, bool $subir = false): void
    {
        $muestra = $bajan;
        usort($muestra, fn ($a, $b) => $b['stock'] <=> $a['stock']);

        $this->table(
            ['Producto', 'Hoy', 'Nuevo', $subir ? 'Sube' : 'Baja', 'Costo', 'Público', 'Margen', 'Stock'],
            array_map(fn ($b) => [
                mb_substr($b['nombre'], 0, 34),
                '$' . number_format($b['actual'], 0, ',', '.'),
                '$' . number_format($b['nuevo'], 0, ',', '.'),
                ($subir ? '+$' : '-$') . number_format(abs($b['baja']), 0, ',', '.'),
                '$' . number_format($b['costo'], 0, ',', '.'),
                $b['publico'] > 0 ? '$' . number_format($b['publico'], 0, ',', '.') : '—',
                round($b['margen'], 1) . '%',
                number_format($b['stock'], 0, ',', '.'),
            ], array_slice($muestra, 0, 25))
        );
    }

    private function mostrarResumen(array $bajan, int $sinTocar, int $sinMatch, float $piso, bool $subir = false): void
    {
        $margenes = array_column($bajan, 'margen');
        sort($margenes);
        $n = count($margenes);
        $deltas = array_map('abs', array_column($bajan, 'baja'));

        // Cuántos quedan por encima del precio público de Miracle (sólo informativo).
        $sobrePublico = count(array_filter(
            $bajan,
            fn ($b) => $b['publico'] > 0 && $b['nuevo'] > $b['publico']
        ));

        $this->info('── Resumen ──');
        $this->line('  Productos que ' . ($subir ? 'suben' : 'bajan') . ':    ' . $n);
        $this->line('  Sin tocar:               ' . $sinTocar . ($subir ? ' (ya están sobre el piso)' : ' (no se puede bajar sin romper el piso)'));
        $this->line('  Sin match en Miracle:    ' . $sinMatch);
        $this->line('  Ajuste promedio:         ' . ($subir ? '+' : '-') . '$' . number_format(array_sum($deltas) / max(1, $n), 0, ',', '.'));
        $this->line('  Ajuste máximo:           ' . ($subir ? '+' : '-') . '$' . number_format(max($deltas), 0, ',', '.'));
        $this->line('  Margen mínimo resultante: ' . round($margenes[0], 1) . '%  (piso pedido: ' . $piso . '%)');
        $this->line('  Margen mediano:          ' . round($margenes[(int) ($n * 0.5)], 1) . '%');
        $this->line('  Productos en pérdida:    ' . count(array_filter($margenes, fn ($m) => $m < 0)));

        if ($subir && $sobrePublico > 0) {
            $this->newLine();
            $this->comment('  Nota: ' . $sobrePublico . ' producto(s) quedan por encima del precio público de Miracle.');
            $this->comment('  Es esperable: Miracle vende al por mayor a salones y tú al detal a consumidora final.');
        }
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
