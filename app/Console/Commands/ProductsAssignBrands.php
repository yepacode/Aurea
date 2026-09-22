<?php

namespace App\Console\Commands;

use App\Models\Brand;
use App\Models\Product;
use Illuminate\Console\Command;

/**
 * Asigna `brand_id` a productos que lo tengan NULL, matcheando por keyword
 * en el nombre. Si no hay match, cae al brand default (aurea-naturals).
 *
 * Sin brand_id el schema.org `Brand` y el feed Google Merchant `g:brand`
 * salen genéricos → dañan CTR en SERP y elegibilidad en Shopping.
 *
 * Uso:
 *   php artisan products:assign-brands --dry-run
 *   php artisan products:assign-brands
 */
class ProductsAssignBrands extends Command
{
    protected $signature = 'products:assign-brands {--dry-run : mostrar asignación sin guardar} {--default=aurea-naturals : slug del brand a usar si no hay match}';

    protected $description = 'Asigna brand_id por keyword en el nombre; fallback al brand default (aurea-naturals).';

    public function handle(): int
    {
        $dry         = (bool) $this->option('dry-run');
        $defaultSlug = (string) $this->option('default');

        $brands = Brand::all(['id', 'name', 'slug']);
        if ($brands->isEmpty()) {
            $this->error('No hay brands en la BD.');
            return self::FAILURE;
        }

        $default = $brands->firstWhere('slug', $defaultSlug) ?? $brands->first();
        $this->info("Brand default: {$default->slug} (#{$default->id})");

        // Precomputar keywords por brand — nombre en minúscula, slug, y slug con guiones a espacios.
        $keywordsByBrand = [];
        foreach ($brands as $b) {
            $keywordsByBrand[$b->id] = array_unique(array_filter([
                mb_strtolower($b->name),
                mb_strtolower($b->slug),
                mb_strtolower(str_replace('-', ' ', $b->slug)),
                // Primera palabra distintiva (wuhao, sage, bella, la, aurea, rose).
                mb_strtolower(explode(' ', $b->name)[0] ?? ''),
            ]));
        }

        $assigned = 0;
        $distribution = [];

        Product::whereNull('brand_id')->orderBy('id')->chunkById(100, function ($chunk) use ($brands, $default, $keywordsByBrand, &$assigned, &$distribution, $dry) {
            foreach ($chunk as $p) {
                $matched = null;
                $name = mb_strtolower($p->name);

                foreach ($brands as $b) {
                    foreach ($keywordsByBrand[$b->id] as $k) {
                        // Sólo matchea si el keyword tiene ≥3 chars (evita "la" → La Rose).
                        if (mb_strlen($k) < 3) continue;
                        if (mb_strpos($name, $k) !== false) {
                            $matched = $b;
                            break 2;
                        }
                    }
                }

                $target = $matched ?? $default;
                $distribution[$target->slug] = ($distribution[$target->slug] ?? 0) + 1;

                if (! $dry) {
                    Product::whereKey($p->id)->update(['brand_id' => $target->id]);
                }
                $assigned++;
            }
        });

        $this->info(($dry ? '[DRY-RUN] ' : '') . "Asignados: {$assigned}");
        $this->line('');
        $this->line('Distribución por brand:');
        foreach ($distribution as $slug => $n) {
            $this->line("  {$slug}: {$n}");
        }

        return self::SUCCESS;
    }
}
