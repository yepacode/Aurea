<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;

class SeoAutofill extends Command
{
    protected $signature = 'seo:autofill {--dry-run : mostrar sin guardar}';

    protected $description = 'Rellena meta_description y focus_keyword vacíos en productos activos, usando su descripción y nombre.';

    public function handle(): int
    {
        $dry = (bool) $this->option('dry-run');

        $q = Product::active()->where(function ($q) {
            $q->whereNull('meta_description')
                ->orWhere('meta_description', '')
                ->orWhereNull('focus_keyword')
                ->orWhere('focus_keyword', '');
        });

        $count = (clone $q)->count();
        $this->info("Productos a rellenar: {$count}".($dry ? ' (dry-run)' : ''));

        $updated = 0;
        $q->chunkById(100, function ($chunk) use (&$updated, $dry) {
            foreach ($chunk as $p) {
                $update = [];

                if (empty($p->meta_description)) {
                    $desc = mb_substr(trim(strip_tags($p->description ?? '')), 0, 155);
                    if (empty($desc)) {
                        $desc = 'Descubre '.$p->name.' en Belleza Áurea. Insumos y cosmética profesional con envío a toda Colombia.';
                    }
                    $update['meta_description'] = $desc;
                }

                if (empty($p->focus_keyword)) {
                    $update['focus_keyword'] = mb_substr($p->name, 0, 120);
                }

                if (! empty($update)) {
                    if (! $dry) {
                        $p->update($update);
                    }
                    $updated++;
                }
            }
        });

        $this->info(($dry ? 'Serían' : 'Fueron').' actualizados: '.$updated);

        return self::SUCCESS;
    }
}
