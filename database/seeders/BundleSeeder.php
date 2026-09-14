<?php

namespace Database\Seeders;

use App\Models\Bundle;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BundleSeeder extends Seeder
{
    /**
     * Genera 3 bundles de ejemplo si hay stock de productos suficiente.
     * NO borra bundles existentes; solo agrega los que faltan por slug.
     */
    public function run(): void
    {
        $totalProducts = Product::active()->count();
        if ($totalProducts < 3) {
            $this->command?->warn('Muy pocos productos activos: no se generaron kits de ejemplo.');
            return;
        }

        $plans = [
            [
                'name'  => 'Ritual completo cutícula',
                'desc'  => 'Todo lo que necesitas para un cuidado impecable: aceite hidratante, lima suave y tu esmalte favorito. Un ritual sensorial de principio a fin.',
                'query' => fn () => $this->pickByCategoryLike(['uñas', 'unas', 'nails']) ?? $this->pickAny(3),
                'discount_pct' => 15,
            ],
            [
                'name'  => 'Descubre Áurea Naturals',
                'desc'  => 'Cuatro imprescindibles para conocer la esencia de la marca: ingredientes botánicos, fórmulas suaves y resultados visibles.',
                'query' => fn () => $this->pickByBrandLike(['Áurea', 'Aurea', 'Naturals', 'Áurea Naturals']) ?? $this->pickAny(4),
                'discount_pct' => 20,
            ],
            [
                'name'  => 'Kit primeros pasos',
                'desc'  => 'Perfecto para empezar tu rutina de cuidado: tres productos accesibles pensados para el día a día.',
                'query' => fn () => Product::active()->where('price', '>', 0)->orderBy('price')->take(3)->get(),
                'discount_pct' => 10,
            ],
        ];

        foreach ($plans as $i => $plan) {
            $slug = Str::slug($plan['name']);
            if (Bundle::where('slug', $slug)->exists()) {
                $this->command?->info("Kit «{$plan['name']}» ya existe. Se omite.");
                continue;
            }

            $items = $plan['query']();
            if (! $items || $items->count() < 2) {
                $this->command?->warn("Sin productos suficientes para «{$plan['name']}». Se omite.");
                continue;
            }

            $compare = (int) $items->sum(fn ($p) => (int) $p->price);
            if ($compare <= 0) {
                $this->command?->warn("Precio comparativo 0 para «{$plan['name']}». Se omite.");
                continue;
            }
            $price = (int) round($compare * (1 - $plan['discount_pct'] / 100));

            $bundle = Bundle::create([
                'name'          => $plan['name'],
                'slug'          => $slug,
                'description'   => $plan['desc'],
                'price'         => $price,
                'compare_price' => $compare,
                'is_active'     => true,
                'sort_order'    => $i,
            ]);

            $sync = [];
            foreach ($items->values() as $idx => $p) {
                $sync[$p->id] = ['quantity' => 1, 'sort_order' => $idx];
            }
            $bundle->items()->sync($sync);

            $this->command?->info("Kit creado: {$bundle->name} — ahorro {$plan['discount_pct']}% (\${$bundle->savings})");
        }
    }

    private function pickByCategoryLike(array $needles)
    {
        foreach ($needles as $needle) {
            $cat = Category::where('name', 'like', "%{$needle}%")->first();
            if (! $cat) continue;
            $products = Product::active()->where('category_id', $cat->id)
                ->where('price', '>', 0)
                ->inRandomOrder()->take(3)->get();
            if ($products->count() >= 2) return $products;
        }
        return null;
    }

    private function pickByBrandLike(array $needles)
    {
        foreach ($needles as $needle) {
            $brand = \App\Models\Brand::where('name', 'like', "%{$needle}%")->first();
            if (! $brand) continue;
            $products = Product::active()->where('brand_id', $brand->id)
                ->where('price', '>', 0)
                ->inRandomOrder()->take(4)->get();
            if ($products->count() >= 2) return $products;
        }
        return null;
    }

    private function pickAny(int $n)
    {
        return Product::active()->where('price', '>', 0)->inRandomOrder()->take($n)->get();
    }
}
