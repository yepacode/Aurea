<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Sincroniza el catálogo de Áurea jalando el JSON del endpoint de Miracle.
 *
 * SOLO backend / por URL: no crea pantallas ni toca el front. Empareja por
 * internal_code (Áurea) == referencia (Miracle), en minúsculas.
 *
 * Qué actualiza (sobre productos que YA existen en Áurea — nunca crea nuevos):
 *   • stock            -> stock disponible de Miracle
 *   • precios Miracle  -> miracle_precio_centro / miracle_precio_distribuidor
 *                         (SOLO informativos; NO toca el precio de venta `price`)
 *   • imágenes         -> solo si cambian en Miracle (firma) o si el producto
 *                         no tiene imágenes; se optimizan a WebP
 *   • textos           -> name/description SOLO con --con-textos (por defecto NO,
 *                         para no pisar el SEO ya trabajado en Áurea)
 *
 * Uso:
 *   php artisan aurea:sync-miracle                 # sync normal (6am diario)
 *   php artisan aurea:sync-miracle --dry-run       # simula, no escribe
 *   php artisan aurea:sync-miracle --con-imagenes  # fuerza re-descarga de imágenes
 *   php artisan aurea:sync-miracle --sin-imagenes  # omite imágenes (más rápido)
 *   php artisan aurea:sync-miracle --con-textos    # también actualiza name/description
 *   php artisan aurea:sync-miracle --solo=1001     # solo una referencia (pruebas)
 */
class SyncMiracleProducts extends Command
{
    protected $signature = 'aurea:sync-miracle
                            {--dry-run : No escribe nada, solo reporta}
                            {--con-imagenes : Fuerza re-descarga de imágenes aunque no hayan cambiado}
                            {--sin-imagenes : No procesa imágenes}
                            {--con-textos : También actualiza name y description desde Miracle}
                            {--solo= : Sincroniza solo esa referencia (para pruebas)}';

    protected $description = 'Sincroniza stock, precios e imágenes desde el catálogo de Miracle (por URL)';

    private const MAX_DIM = 1200;
    private const WEBP_QUALITY = 85;

    public function handle(): int
    {
        @ini_set('memory_limit', '2048M');
        set_time_limit(0);

        $url   = (string) config('miracle.api_url');
        $token = (string) config('miracle.api_token');
        $timeout = (int) config('miracle.timeout', 120);

        if ($url === '' || $token === '') {
            $this->error('Falta configurar MIRACLE_API_URL / MIRACLE_API_TOKEN en el .env.');
            return self::FAILURE;
        }

        $dryRun   = (bool) $this->option('dry-run');
        $soloRef  = $this->option('solo');
        $forzarImg = (bool) $this->option('con-imagenes');
        $sinImg   = (bool) $this->option('sin-imagenes');
        $conTextos = (bool) $this->option('con-textos');

        $this->info('==> Sincronización Miracle → Belleza Áurea');
        $this->line('    URL:      '.$url);
        $this->line('    Modo:     '.($dryRun ? 'DRY RUN (no escribe)' : 'REAL'));
        $this->line('    Imágenes: '.($sinImg ? 'omitidas' : ($forzarImg ? 'forzar re-descarga' : 'solo si cambian')));
        $this->line('    Textos:   '.($conTextos ? 'SÍ actualiza name/description' : 'NO (protege SEO)'));
        if ($soloRef) {
            $this->line('    Solo ref: '.$soloRef);
        }
        $this->newLine();

        // --- 1) Traer el catálogo de Miracle --------------------------------
        $this->line('Consultando Miracle...');
        $productosMiracle = $this->fetchMiracle($url, $token, $timeout, $soloRef);
        if ($productosMiracle === null) {
            return self::FAILURE;
        }
        $this->info('Productos recibidos de Miracle: '.count($productosMiracle));

        // --- 2) Index de productos de Áurea por internal_code ---------------
        $aureaByCode = Product::all()->keyBy(fn ($p) => Str::lower((string) $p->internal_code));
        $this->info('Productos en Áurea: '.$aureaByCode->count());
        $this->newLine();

        $stats = [
            'match' => 0, 'no_match' => 0,
            'stock_upd' => 0, 'precio_upd' => 0, 'texto_upd' => 0,
            'img_import' => 0, 'img_skip' => 0, 'img_fail' => 0,
            'no_match_refs' => [],
        ];

        $bar = $this->output->createProgressBar(count($productosMiracle));
        $bar->start();

        foreach ($productosMiracle as $m) {
            $bar->advance();

            $ref = Str::lower(trim((string) ($m['referencia'] ?? '')));
            if ($ref === '') {
                continue;
            }

            /** @var Product|null $prod */
            $prod = $aureaByCode[$ref] ?? null;
            if (! $prod) {
                $stats['no_match']++;
                if (count($stats['no_match_refs']) < 40) {
                    $stats['no_match_refs'][] = $m['referencia'] ?? $ref;
                }
                continue;
            }

            $stats['match']++;
            $cambios = false;

            // --- Stock (disponible de Miracle) ---
            $stockNuevo = max(0, (int) ($m['stock']['disponible'] ?? 0));
            if ((int) $prod->stock !== $stockNuevo) {
                $prod->stock = $stockNuevo;
                $stats['stock_upd']++;
                $cambios = true;
            }

            // --- Precios informativos (NO toca price) ---
            $pc = $m['precios']['centro_experiencia'] ?? null;
            $pd = $m['precios']['distribuidor'] ?? null;
            if ($this->distintoDecimal($prod->miracle_precio_centro, $pc)
                || $this->distintoDecimal($prod->miracle_precio_distribuidor, $pd)) {
                $prod->miracle_precio_centro = $pc;
                $prod->miracle_precio_distribuidor = $pd;
                $stats['precio_upd']++;
                $cambios = true;
            }

            // --- Textos (opt-in) ---
            if ($conTextos) {
                $nombre = trim((string) ($m['nombre'] ?? ''));
                $desc = $m['descripcion'] ?? null;
                if ($nombre !== '' && $prod->name !== $nombre) {
                    $prod->name = $nombre;
                    $cambios = true;
                    $stats['texto_upd']++;
                }
                if ($desc !== null && $prod->description !== $desc) {
                    $prod->description = $desc;
                    $cambios = true;
                }
            }

            // --- Imágenes (solo si cambian, salvo flags) ---
            if (! $sinImg) {
                $galeria = array_values(array_filter((array) ($m['galeria'] ?? [])));
                $nuevaFirma = $galeria ? md5(implode('|', $galeria)) : '';
                $tieneImgs = ! empty($prod->images);

                $debeImportar = ! empty($galeria) && (
                    $forzarImg
                    || ! $tieneImgs
                    || ($prod->miracle_img_sig !== null && $prod->miracle_img_sig !== $nuevaFirma)
                );

                if ($debeImportar) {
                    if ($dryRun) {
                        $stats['img_import']++;
                    } else {
                        $res = $this->importarImagenes($prod, $galeria);
                        $stats['img_import'] += $res['ok'];
                        $stats['img_fail']   += $res['fail'];
                        if ($res['ok'] > 0) {
                            $prod->images = $res['paths'];
                            $cambios = true;
                        }
                    }
                } else {
                    $stats['img_skip']++;
                }

                // Registrar la firma actual aunque no se re-descargue.
                if ($prod->miracle_img_sig !== $nuevaFirma) {
                    $prod->miracle_img_sig = $nuevaFirma;
                    $cambios = true;
                }
            }

            // --- Metadatos de sync ---
            $prod->miracle_ref = $m['referencia'] ?? $prod->internal_code;
            $prod->miracle_synced_at = now();

            if (! $dryRun) {
                $prod->save();
            }
        }

        $bar->finish();
        $this->newLine(2);

        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->info(' RESUMEN'.($dryRun ? '  (DRY RUN)' : ''));
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->line('  Emparejados:            '.$stats['match']);
        $this->line('  Sin match en Áurea:     '.$stats['no_match']);
        $this->line('  Stock actualizado:      '.$stats['stock_upd']);
        $this->line('  Precios actualizados:   '.$stats['precio_upd']);
        if ($conTextos) {
            $this->line('  Nombres actualizados:   '.$stats['texto_upd']);
        }
        if (! $sinImg) {
            $this->line('  Imágenes importadas:    '.$stats['img_import']);
            $this->line('  Productos img sin cambio:'.$stats['img_skip']);
            $this->line('  Imágenes con error:     '.$stats['img_fail']);
        }

        if (! empty($stats['no_match_refs'])) {
            $this->newLine();
            $this->warn('Refs de Miracle sin producto en Áurea (muestra):');
            $this->line('  '.implode(', ', $stats['no_match_refs']));
        }

        if ($dryRun) {
            $this->newLine();
            $this->warn('DRY RUN — no se guardó nada. Quita --dry-run para aplicar.');
        }

        return self::SUCCESS;
    }

    /**
     * Trae todos los productos del endpoint de Miracle (sigue paginación si la hay).
     */
    private function fetchMiracle(string $url, string $token, int $timeout, ?string $soloRef): ?array
    {
        $todos = [];
        $page = 1;

        do {
            $params = ['token' => $token];
            if ($soloRef) {
                $params['referencia'] = $soloRef;
            }
            if ($page > 1) {
                $params['page'] = $page;
            }

            try {
                $resp = Http::timeout($timeout)->acceptJson()->get($url, $params);
            } catch (\Throwable $e) {
                $this->error('No se pudo conectar con Miracle: '.$e->getMessage());
                return null;
            }

            if (! $resp->successful()) {
                $this->error('Miracle respondió HTTP '.$resp->status().': '.Str::limit($resp->body(), 200));
                return null;
            }

            $json = $resp->json();
            if (! is_array($json) || empty($json['success'])) {
                $this->error('Respuesta inesperada de Miracle.');
                return null;
            }

            foreach (($json['data'] ?? []) as $row) {
                $todos[] = $row;
            }

            $totalPaginas = (int) ($json['meta']['total_paginas'] ?? 1);
            $page++;
        } while ($page <= $totalPaginas);

        return $todos;
    }

    /**
     * Descarga, optimiza a WebP y guarda las imágenes de un producto.
     * Reemplaza las imágenes previas del producto (limpia su carpeta).
     *
     * @param  string[]  $urls
     * @return array{ok:int,fail:int,paths:string[]}
     */
    private function importarImagenes(Product $prod, array $urls): array
    {
        $ok = 0; $fail = 0;
        $folder = 'products/'.$prod->slug;

        // --- Fase 1: descargar + optimizar TODO en memoria -------------------
        // Importante: no borramos nada hasta tener las nuevas imágenes listas,
        // así una fuente caída (404) nunca deja al producto sin imágenes.
        $nuevas = [];
        foreach ($urls as $idx => $u) {
            try {
                $resp = Http::timeout(60)->get($u);
                if (! $resp->successful()) {
                    $fail++;
                    continue;
                }
                $webp = $this->optimizeToWebP($resp->body());
                if ($webp === null) {
                    $fail++;
                    continue;
                }
                $nuevas[] = ['idx' => $idx, 'webp' => $webp];
                $ok++;
            } catch (\Throwable $e) {
                $fail++;
            }
        }

        // Si no se pudo bajar NINGUNA, dejamos las imágenes actuales intactas.
        if ($ok === 0) {
            return ['ok' => 0, 'fail' => $fail, 'paths' => (array) $prod->images];
        }

        // --- Fase 2: ya con imágenes nuevas, reemplazamos ---------------------
        foreach ((array) $prod->images as $old) {
            if (is_string($old) && Str::startsWith($old, $folder.'/') && Storage::disk('public')->exists($old)) {
                Storage::disk('public')->delete($old);
            }
        }
        Storage::disk('public')->makeDirectory($folder);

        $paths = [];
        foreach ($nuevas as $n) {
            $relPath = $folder.'/'.($n['idx'] + 1).'-'.uniqid().'.webp';
            Storage::disk('public')->put($relPath, $n['webp']);
            $paths[] = $relPath;
        }

        return ['ok' => $ok, 'fail' => $fail, 'paths' => array_values(array_unique($paths))];
    }

    private function optimizeToWebP(string $bytes): ?string
    {
        $img = @imagecreatefromstring($bytes);
        if (! $img) {
            return null;
        }

        if (function_exists('imageistruecolor') && ! imageistruecolor($img)) {
            imagepalettetotruecolor($img);
        }

        $w = imagesx($img); $h = imagesy($img);
        if (max($w, $h) > self::MAX_DIM) {
            if ($w >= $h) {
                $newW = self::MAX_DIM;
                $newH = (int) round($h * self::MAX_DIM / $w);
            } else {
                $newH = self::MAX_DIM;
                $newW = (int) round($w * self::MAX_DIM / $h);
            }
            $resized = imagecreatetruecolor($newW, $newH);
            imagealphablending($resized, false);
            imagesavealpha($resized, true);
            $transparent = imagecolorallocatealpha($resized, 0, 0, 0, 127);
            imagefilledrectangle($resized, 0, 0, $newW, $newH, $transparent);
            imagecopyresampled($resized, $img, 0, 0, 0, 0, $newW, $newH, $w, $h);
            imagedestroy($img);
            $img = $resized;
        } else {
            imagealphablending($img, false);
            imagesavealpha($img, true);
        }

        ob_start();
        imagewebp($img, null, self::WEBP_QUALITY);
        $out = ob_get_clean();
        imagedestroy($img);

        return $out !== false && strlen($out) > 0 ? $out : null;
    }

    /**
     * ¿Dos valores decimales (uno guardado como string/decimal, otro del JSON) difieren?
     */
    private function distintoDecimal($a, $b): bool
    {
        if ($a === null && $b === null) {
            return false;
        }
        if ($a === null || $b === null) {
            return true;
        }
        return abs((float) $a - (float) $b) > 0.001;
    }
}
