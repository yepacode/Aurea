<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;

class ProductsTrimTruncation extends Command
{
    protected $signature = 'products:trim-truncation {--dry-run}';

    protected $description = 'Detecta y limpia productos con descripción terminada en "..." (truncada en import). Corta hasta la última frase completa o marca para revisión.';

    public function handle(): int
    {
        $dry = $this->option('dry-run');

        // Trae todos los IDs de una (son pocos ~237 y así evitamos el bucle
        // de chunkById cuando el WHERE se basa en el mismo campo que updateas).
        $ids = Product::whereRaw("TRIM(description) LIKE '%...' OR TRIM(description) LIKE '%…'")
            ->pluck('id')
            ->all();

        $count = count($ids);
        $this->info("Productos con descripción truncada: {$count}" . ($dry ? ' (dry-run)' : ''));

        if ($count === 0) {
            return self::SUCCESS;
        }

        $fixed = 0;
        $flagged = 0;

        $bar = $this->output->createProgressBar($count);
        $bar->start();

        foreach (array_chunk($ids, 100) as $chunkIds) {
            $products = Product::whereIn('id', $chunkIds)->get();

            foreach ($products as $p) {
                $desc = trim(preg_replace('/[.]{2,}$|…$/u', '', trim((string) $p->description)));

                $stops = [];
                foreach (['. ', '! ', '? '] as $sep) {
                    $pos = mb_strrpos($desc, $sep);
                    if ($pos !== false) {
                        $stops[] = $pos;
                    }
                }
                $lastStop = ! empty($stops) ? max($stops) : -1;

                if ($lastStop > 40) {
                    // Deja hasta el signo (incluyéndolo).
                    $newDesc = mb_substr($desc, 0, $lastStop + 1);
                    if (! $dry) {
                        $p->update(['description' => $newDesc]);
                    }
                    $fixed++;
                } else {
                    // Sin punto útil — le agregamos punto final y ya.
                    $newDesc = rtrim($desc, '.,;: ') . '.';
                    if (! $dry) {
                        $p->update(['description' => $newDesc]);
                    }
                    $flagged++;
                }

                $bar->advance();
            }
        }

        $bar->finish();
        $this->newLine();

        $this->info(($dry ? 'Serían' : 'Fueron') . ' recortados hasta última frase completa: ' . $fixed);
        $this->info(($dry ? 'Serían' : 'Fueron') . ' cerrados con punto final: ' . $flagged);

        return self::SUCCESS;
    }
}
