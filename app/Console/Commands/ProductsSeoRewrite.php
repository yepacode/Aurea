<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

/**
 * Rewrite masivo de meta_title + meta_description reales para todos los
 * productos activos. Además normaliza `name` a Title Case si viene en
 * MAYÚSCULAS TOTAL (misma lógica que `products:title-case`).
 *
 * - meta_title    : "{Name} — {Categoría} | Belleza Áurea"  (≤60 chars)
 * - meta_desc     : plantilla por categoría (≤160 chars)
 *
 * Sólo escribe si `meta_title` está vacío o si `meta_description` es
 * inservible (<100 chars). Todo lo demás se respeta (skip).
 *
 * Uso:
 *   php artisan products:seo-rewrite --dry-run
 *   php artisan products:seo-rewrite
 */
class ProductsSeoRewrite extends Command
{
    protected $signature = 'products:seo-rewrite {--dry-run : mostrar cambios sin guardar}';

    protected $description = 'Genera meta_title y meta_description reales para productos activos, y normaliza el nombre a Title Case si es MAYÚSCULA TOTAL.';

    private const BRAND = 'Belleza Áurea';

    /** Preposiciones/artículos que quedan en minúscula (excepto en 1ª posición). */
    private array $lowerWords = [
        'de', 'del', 'la', 'las', 'el', 'los',
        'y', 'o', 'u', 'e', 'a', 'en', 'con',
        'para', 'por', 'sin', 'al', 'sobre',
    ];

    /** Unidades siempre minúsculas. */
    private array $unitWords = [
        'ml', 'g', 'gr', 'grs', 'kg', 'mg', 'oz',
        'cm', 'mm', 'm', 'l', 'lt', 'lts', 'un', 'unds', 'und',
    ];

    public function handle(): int
    {
        $dry = (bool) $this->option('dry-run');

        $updated = 0;
        $skipped = 0;
        $renamed = 0;
        $samples = [];

        Product::active()->with('category')->orderBy('id')->chunkById(100, function ($chunk) use (&$updated, &$skipped, &$renamed, &$samples, $dry) {
            foreach ($chunk as $p) {
                $payload = [];

                // 1) Normalizar name si viene todo en mayúsculas.
                $origName = (string) $p->name;
                $name     = $origName;
                if ($this->isAllUpper($origName)) {
                    $name = $this->toTitleCase($origName);
                    if ($name !== $origName) {
                        $payload['name'] = $name;
                        $renamed++;
                    }
                }

                // 2) meta_title — sólo si viene vacío.
                $needsTitle = empty(trim((string) $p->meta_title));
                $newTitle   = $needsTitle ? $this->buildTitle($name, $p) : $p->meta_title;
                if ($needsTitle) {
                    $payload['meta_title'] = $newTitle;
                }

                // 3) meta_description — sólo si viene vacío o <100 chars (nombre repetido).
                $currentDesc = trim((string) $p->meta_description);
                $needsDesc   = ($currentDesc === '' || mb_strlen($currentDesc) < 100);
                $newDesc     = $needsDesc ? $this->buildDescription($name, $p) : $currentDesc;
                if ($needsDesc) {
                    $payload['meta_description'] = $newDesc;
                }

                if (empty($payload)) {
                    $skipped++;
                    continue;
                }

                if (count($samples) < 5) {
                    $samples[] = [
                        'id'         => $p->id,
                        'name'       => $payload['name'] ?? $p->name,
                        'meta_title' => $payload['meta_title'] ?? '(sin cambio)',
                        'meta_desc'  => $payload['meta_description'] ?? '(sin cambio)',
                    ];
                }

                if (! $dry) {
                    // Update directo — evita disparar hooks del modelo y no toca otras columnas.
                    Product::whereKey($p->id)->update($payload);
                }
                $updated++;
            }
        });

        $this->info(($dry ? '[DRY-RUN] ' : '') . "Actualizados: {$updated}  |  Sin cambios: {$skipped}  |  Nombres normalizados: {$renamed}");

        if (! empty($samples)) {
            $this->line('');
            $this->line('Primeros 5 ejemplos:');
            foreach ($samples as $s) {
                $this->line("  #{$s['id']}  {$s['name']}");
                $this->line("     title: {$s['meta_title']} (" . mb_strlen($s['meta_title']) . " chars)");
                $this->line("     desc : {$s['meta_desc']} (" . mb_strlen($s['meta_desc']) . " chars)");
                $this->line('');
            }
        }

        return self::SUCCESS;
    }

    /**
     * meta_title: "{Name} — {Categoría} | Belleza Áurea" recortado a 60 chars.
     * Si al recortar queda largo, prioriza {Name} + " | Belleza Áurea".
     */
    private function buildTitle(string $name, Product $p): string
    {
        $category = $p->category?->name;
        $brand    = self::BRAND;

        if ($category) {
            $full = "{$name} — {$category} | {$brand}";
            if (mb_strlen($full) <= 60) {
                return $full;
            }
        }

        // Fallback: {Name} | Marca (o sólo Name si aún es largo).
        $short = "{$name} | {$brand}";
        if (mb_strlen($short) <= 60) {
            return $short;
        }

        // Último recurso: truncar el nombre para respetar el límite.
        $room = 60 - mb_strlen(" | {$brand}");
        return rtrim(mb_substr($name, 0, max($room, 20)), '- ') . " | {$brand}";
    }

    /**
     * meta_description: plantilla por categoría (≤160 chars).
     */
    private function buildDescription(string $name, Product $p): string
    {
        $categorySlug = $p->category?->slug ?? '';
        $categoryName = $p->category?->name ?? '';

        // Descripción corta a partir de la descripción real (si existe) — para inyectar en plantillas.
        $short = trim(strip_tags((string) $p->description));
        $short = $short !== '' ? mb_substr($short, 0, 80) : '';

        $tpl = $this->descriptionTemplateFor($categorySlug, $categoryName, $name, $short);

        return $this->clipTo($tpl, 160);
    }

    private function descriptionTemplateFor(string $slug, string $categoryName, string $name, string $short): string
    {
        // Familias por slug (agrupamos categorías afines).
        $isUnas       = Str::contains($slug, ['unas', 'esmaltes', 'brocas', 'limas', 'construccion', 'preparadores', 'remocion']);
        $isRostro     = Str::contains($slug, ['rostro', 'piel']);
        $isPestanas   = Str::contains($slug, ['pestanas']);
        $isPeluqueria = Str::contains($slug, ['peluqueria', 'cepillos', 'tijeras', 'gorros']);
        $isDeco       = Str::contains($slug, ['decoracion']);
        $isDepilacion = Str::contains($slug, ['depilacion']);
        $isHigiene    = Str::contains($slug, ['seguridad', 'higiene']);
        $isCombos     = Str::contains($slug, ['combos', 'bledos', 'blends']);

        if ($isUnas) {
            $body = $short !== '' ? $short : 'producto premium para uñas perfectas';
            return "Descubre {$name} en Belleza Áurea — {$body}. Envío a toda Colombia y descuento en tu primera compra.";
        }
        if ($isRostro) {
            $feature = $short !== '' ? $short : 'de belleza consciente y calidad profesional';
            return "{$name} — ritual facial {$feature}. Envío nacional y regalo de bienvenida en tu primera compra.";
        }
        if ($isPestanas) {
            return "{$name} para pestañas seductoras. Aplicación profesional en casa. Envíos a toda Colombia con Belleza Áurea. 10% off primera compra.";
        }
        if ($isPeluqueria) {
            return "Cuidado profesional para tu cabello. Descubre {$name} en Belleza Áurea — envío rápido a toda Colombia.";
        }
        if ($isDeco) {
            return "{$name} — accesorio para elevar tus rituales de belleza. Diseño y calidad Belleza Áurea con envío a toda Colombia.";
        }
        if ($isDepilacion) {
            return "{$name} para una depilación suave y efectiva en casa. Envíos a toda Colombia con Belleza Áurea. 10% off en tu primera compra.";
        }
        if ($isHigiene) {
            return "{$name} — higiene y seguridad profesional para tu estudio. Insumos premium en Belleza Áurea con envío a toda Colombia.";
        }
        if ($isCombos) {
            return "{$name} — combo curado por Belleza Áurea. Rituales de belleza premium con envío a toda Colombia y 10% off primera compra.";
        }

        // Default: usa el nombre de categoría cuando lo tenemos.
        if ($categoryName !== '') {
            return "{$name} — {$categoryName} en Belleza Áurea. Calidad premium con envío a toda Colombia y 10% off en tu primera compra.";
        }
        return "{$name} — insumo de belleza en Belleza Áurea. Calidad premium con envío a toda Colombia y 10% off en tu primera compra.";
    }

    /**
     * Recorta a $max chars respetando palabras completas (no corta a mitad de palabra).
     */
    private function clipTo(string $s, int $max): string
    {
        if (mb_strlen($s) <= $max) {
            return $s;
        }
        $cut = mb_substr($s, 0, $max);
        // Retroceder hasta el último espacio para no cortar palabras.
        $pos = mb_strrpos($cut, ' ');
        if ($pos !== false && $pos > $max - 20) {
            $cut = mb_substr($cut, 0, $pos);
        }
        return rtrim($cut, ",;:-— ") . '…';
    }

    /**
     * ¿La cadena está enteramente en mayúsculas? (ignora números, símbolos, espacios).
     */
    private function isAllUpper(string $s): bool
    {
        // Extraer solo letras — si hay letras y NO hay letras minúsculas → todo mayúsculas.
        if (! preg_match('/\p{L}/u', $s)) {
            return false;
        }
        return ! preg_match('/\p{Ll}/u', $s);
    }

    /**
     * Copia adaptada de ProductsTitleCase::toTitleCase para uso interno.
     * Regla: preposiciones/artículos en minúscula (excepto la 1ª palabra),
     * unidades siempre en minúscula, acrónimos (2+ mayús consecutivas)
     * preservados, tokens con dígitos preservados.
     */
    private function toTitleCase(string $s): string
    {
        $s = trim(preg_replace('/\s+/u', ' ', $s));
        if ($s === '') return $s;

        $words = preg_split('/\s+/u', $s);
        foreach ($words as $i => $orig) {
            if ($orig === '') continue;
            $lower = mb_strtolower($orig, 'UTF-8');

            if (in_array($lower, $this->unitWords, true)) {
                $words[$i] = $lower;
                continue;
            }
            if ($lower === 'x' && $i !== 0) {
                $words[$i] = 'x';
                continue;
            }
            if (preg_match('/\d/u', $orig)) {
                $words[$i] = $orig;
                continue;
            }
            if (preg_match('/\p{Lu}{2,}/u', $orig)) {
                // Acrónimo original preservado — pero como venimos de MAYÚSCULAS TOTAL,
                // TODO el nombre coincide con este patrón. Sólo preservamos si el token
                // es corto (≤4 chars): así "UV", "LED", "XXL" quedan, y "ACEITE" se re-caseará.
                if (mb_strlen($orig) <= 4) {
                    $words[$i] = $orig;
                    continue;
                }
            }
            if ($i === 0 || ! in_array($lower, $this->lowerWords, true)) {
                $words[$i] = mb_convert_case($lower, MB_CASE_TITLE, 'UTF-8');
            } else {
                $words[$i] = $lower;
            }
        }
        return implode(' ', $words);
    }
}
