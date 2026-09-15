<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\SubscriptionPlan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SubscriptionPlanSeeder extends Seeder
{
    /**
     * Crea 2 planes de suscripción demo:
     *  · Ritual Áurea Mensual — 3 productos, cada 30 días, ~15% off
     *  · Kit Descubrimiento Bimensual — 5 productos, cada 60 días, ~20% off
     *
     * NO borra planes existentes; los detecta por slug y salta.
     */
    public function run(): void
    {
        $available = Product::query()->where('price', '>', 0)->orderBy('price')->get();
        if ($available->count() < 3) {
            $this->command?->warn('Muy pocos productos: se omiten los planes de suscripción demo.');
            return;
        }

        $plans = [
            [
                'name'                  => 'Ritual Áurea Mensual',
                'description'           => 'Tu ritual esencial cada mes: los 3 productos favoritos de nuestra comunidad, con 15% de descuento por ser suscriptora. Llega puntual a tu puerta.',
                'interval_days'         => 30,
                'discount_percent'      => 15,
                'delivery_days_message' => 'Llega en 3-5 días hábiles',
                'items_count'           => 3,
            ],
            [
                'name'                  => 'Kit Descubrimiento Bimensual',
                'description'           => 'Descubre 5 imprescindibles cada dos meses con 20% de ahorro. Curaduría rotativa para renovar tu ritual sin repetirte.',
                'interval_days'         => 60,
                'discount_percent'      => 20,
                'delivery_days_message' => 'Llega en 3-5 días hábiles',
                'items_count'           => 5,
            ],
        ];

        foreach ($plans as $sort => $data) {
            $slug = Str::slug($data['name']);
            if (SubscriptionPlan::where('slug', $slug)->exists()) {
                $this->command?->info("Plan «{$data['name']}» ya existe. Se omite.");
                continue;
            }

            $productsForPlan = $available->random(min($data['items_count'], $available->count()));
            $regularTotal = 0.0;
            foreach ($productsForPlan as $p) {
                $regularTotal += (float) $p->price;
            }
            $basePrice = round($regularTotal * (100 - $data['discount_percent']) / 100, 2);

            $plan = SubscriptionPlan::create([
                'name'                  => $data['name'],
                'slug'                  => $slug,
                'description'           => $data['description'],
                'base_price'            => $basePrice,
                'regular_price'         => $regularTotal,
                'discount_percent'      => $data['discount_percent'],
                'interval_days'         => $data['interval_days'],
                'delivery_days_message' => $data['delivery_days_message'],
                'is_active'             => true,
                'sort_order'            => $sort,
            ]);

            foreach ($productsForPlan->values() as $i => $p) {
                $plan->items()->create([
                    'product_id' => $p->id,
                    'quantity'   => 1,
                    'sort_order' => $i,
                ]);
            }

            $this->command?->info("Plan «{$data['name']}» creado con {$productsForPlan->count()} producto(s).");
        }
    }
}
