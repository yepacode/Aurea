<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;

class ProductsTitleCase extends Command
{
    protected $signature = 'products:title-case {--dry-run}';

    protected $description = 'Normaliza el campo name de los productos a Title Case, respetando preposiciones y artículos en minúscula.';

    /**
     * Palabras que deben permanecer en minúscula cuando NO son la primera palabra.
     */
    private array $lowerWords = [
        'de', 'del', 'la', 'las', 'el', 'los',
        'y', 'o', 'u', 'e', 'a', 'en', 'con',
        'para', 'por', 'sin', 'al', 'sobre',
    ];

    /**
     * Unidades de medida — se mantienen siempre en minúscula.
     */
    private array $unitWords = [
        'ml', 'g', 'gr', 'grs', 'kg', 'mg', 'oz',
        'cm', 'mm', 'm', 'l', 'lt', 'lts', 'un', 'unds', 'und',
    ];

    public function handle(): int
    {
        $dry = (bool) $this->option('dry-run');
        $updated = 0;
        $skipped = 0;
        $samples = [];

        Product::query()->orderBy('id')->chunkById(100, function ($chunk) use (&$updated, &$skipped, &$samples, $dry) {
            foreach ($chunk as $product) {
                $original = (string) $product->name;
                $normalized = $this->toTitleCase($original);

                if ($original === $normalized) {
                    $skipped++;
                    continue;
                }

                if (count($samples) < 10) {
                    $samples[] = ['id' => $product->id, 'from' => $original, 'to' => $normalized];
                }

                if (! $dry) {
                    // Update solo del campo name; evita disparar hooks costosos y no toca otras columnas.
                    Product::whereKey($product->id)->update(['name' => $normalized]);
                }
                $updated++;
            }
        });

        $this->info(($dry ? '[DRY-RUN] ' : '') . "Actualizados: {$updated}  |  Sin cambios: {$skipped}");

        if (! empty($samples)) {
            $this->line('');
            $this->line('Muestra de cambios:');
            foreach ($samples as $s) {
                $this->line("  #{$s['id']}: \"{$s['from']}\"  →  \"{$s['to']}\"");
            }
        }

        return self::SUCCESS;
    }

    /**
     * Convierte una cadena a Title Case respetando preposiciones/artículos y
     * conservando las mayúsculas que ya tuvieran sentido (ej. siglas simples
     * quedan capitalizadas normalmente).
     */
    private function toTitleCase(string $s): string
    {
        // Colapsar espacios y trim.
        $s = trim(preg_replace('/\s+/u', ' ', $s));
        if ($s === '') {
            return $s;
        }

        // Trabajamos con la cadena original para poder conservar mayúsculas ya intencionales
        // en tokens alfanuméricos (siglas, códigos de color como "C1", "3D", "X3", "10mL").
        $originalWords = preg_split('/\s+/u', $s);

        foreach ($originalWords as $i => $orig) {
            if ($orig === '') {
                continue;
            }
            $lower = mb_strtolower($orig, 'UTF-8');

            // Unidades (ml, g, kg…) siempre en minúscula, incluso al inicio.
            if (in_array($lower, $this->unitWords, true)) {
                $originalWords[$i] = $lower;
                continue;
            }

            // "x" cuando actúa como separador/multiplicador queda en minúscula
            // (ej. "x 15 ml", "Torre x2"). No se aplica si es la primera palabra.
            if ($lower === 'x' && $i !== 0) {
                $originalWords[$i] = 'x';
                continue;
            }

            // Tokens con dígitos: siglas, códigos, medidas pegadas (ej. "3D", "C1", "10mL", "15ml").
            // Los dejamos tal cual estaban en el original para no destruir mayúsculas intencionales.
            if (preg_match('/\d/u', $orig)) {
                $originalWords[$i] = $orig;
                continue;
            }

            // Acrónimos: tokens con 2+ letras consecutivas en mayúsculas (ej. "XXL", "UV", "LED",
            // "UV/LED", "PDF"). Se preservan tal cual — imponer Title Case los estropea ("Xxl").
            if (preg_match('/\p{Lu}{2,}/u', $orig)) {
                $originalWords[$i] = $orig;
                continue;
            }

            // Palabras normales: primera siempre capitalizada; el resto sólo si no está en lowercase list.
            if ($i === 0 || ! in_array($lower, $this->lowerWords, true)) {
                $originalWords[$i] = mb_convert_case($lower, MB_CASE_TITLE, 'UTF-8');
            } else {
                $originalWords[$i] = $lower;
            }
        }

        return implode(' ', $originalWords);
    }
}
