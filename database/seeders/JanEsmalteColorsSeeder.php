<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\Seeder;

/**
 * Carga los ~55 tonos del Esmalte Semipermanente Jan Beauty como variantes de
 * color (swatch con color_hex). Idempotente: si ya existe el tono, no lo duplica.
 *
 * Correr en produccion tras desplegar:
 *   php artisan db:seed --class=Database\\Seeders\\JanEsmalteColorsSeeder --force
 *
 * El stock por tono queda en 10 por defecto; ajustar al inventario real desde el
 * admin (editar producto -> variantes).
 */
class JanEsmalteColorsSeeder extends Seeder
{
    public function run(): void
    {
        $product = Product::where('slug', 'like', 'esmalte-semipermanente-jan-beauty%')->first()
            ?? Product::find(368);

        if (! $product) {
            $this->command?->warn('No se encontro el producto Esmalte Jan Beauty.');
            return;
        }

        // [familia, tono, hex]  (hex: exacto del panel en Gel Polish/Aurora; aproximado en glitter/translucidos/ojo de gato)
        $colors = [
            // Rubber Base Color
            ['Rubber Base', 'Base Rubber 1', '#EDE7E0'],
            ['Rubber Base', 'Base Rubber 2', '#E6E2D8'],
            ['Rubber Base', 'Base Rubber 3', '#F3D9D2'],
            ['Rubber Base', 'Base Rubber 4', '#EFC9C4'],
            // Traslucido
            ['Traslucido', 'Traslucido 01', '#F7E9E4'],
            ['Traslucido', 'Traslucido 02', '#F3DAD0'],
            ['Traslucido', 'Traslucido 03', '#F5D7CE'],
            ['Traslucido', 'Traslucido 04', '#F2CFC6'],
            ['Traslucido', 'Traslucido 05', '#F4C9BE'],
            ['Traslucido', 'Traslucido 06', '#E8B9A8'],
            ['Traslucido', 'Traslucido 07', '#DCCBE0'],
            // Glitter
            ['Glitter', 'Polonia', '#9FC6E8'],
            ['Glitter', 'Singapur', '#C9A36A'],
            ['Glitter', 'Monaco', '#E8B7C4'],
            ['Glitter', 'Jamaica', '#D98BA0'],
            ['Glitter', 'Nepal', '#AEC9A8'],
            ['Glitter', 'Malabia', '#AFC0A0'],
            ['Glitter', 'Espana', '#C9AE6A'],
            ['Glitter', 'Corea', '#B9872E'],
            ['Glitter', 'Malta', '#C98A86'],
            // Gel Polish (muestreado del panel)
            ['Gel Polish', 'Francia', '#0A0A0A'],
            ['Gel Polish', 'Alemania', '#EFF3E8'],
            ['Gel Polish', 'Islandia', '#F3D9D2'],
            ['Gel Polish', 'Italia', '#F4BCD0'],
            ['Gel Polish', 'Portugal', '#9C6B45'],
            ['Gel Polish', 'Escocia', '#BE9A82'],
            ['Gel Polish', 'Turquia', '#6E2A18'],
            ['Gel Polish', 'Egipto', '#A97A82'],
            ['Gel Polish', 'India', '#3A1E18'],
            ['Gel Polish', 'Suecia', '#D6322A'],
            ['Gel Polish', 'Holanda', '#3A2E2E'],
            ['Gel Polish', 'Suiza', '#AA5560'],
            ['Gel Polish', 'Belgica', '#EEC6E0'],
            ['Gel Polish', 'Croacia', '#9E7A90'],
            ['Gel Polish', 'Irlanda', '#E2DEE2'],
            ['Gel Polish', 'Canada', '#BEE8F3'],
            ['Gel Polish', 'Rusia', '#1F22C8'],
            ['Gel Polish', 'China', '#4FE08A'],
            ['Gel Polish', 'Noruega', '#0A1E0A'],
            ['Gel Polish', 'Japon', '#C8E04F'],
            // Aurora
            ['Aurora', 'Boreal', '#C3B3D9'],
            ['Aurora', 'Artica', '#76C6D0'],
            ['Aurora', 'Antartica', '#DAD2D2'],
            ['Aurora', 'Austral', '#ECD2D6'],
            // Tonos Neon
            ['Tonos Neon', 'Martini', '#C724B1'],
            ['Tonos Neon', 'Coco Loco', '#F0225E'],
            ['Tonos Neon', 'Pina Colada', '#F2D21E'],
            ['Tonos Neon', 'Caipirina', '#F26A1B'],
            ['Tonos Neon', 'Mojito', '#2ECC40'],
            // Ojo de Gato
            ['Ojo de Gato', 'Gato Hermano', '#8A8A8A'],
            ['Ojo de Gato', 'Gato Persia', '#9C7A8A'],
            ['Ojo de Gato', 'Gato Bengali', '#C98AA0'],
            ['Ojo de Gato', 'Gato Amarok', '#6A8A5A'],
            ['Ojo de Gato', 'Gato Siames', '#9BBFB0'],
            ['Ojo de Gato', 'Gato Oriental', '#5A1A1A'],
        ];

        $order = 0;
        foreach ($colors as [$familia, $tono, $hex]) {
            // firstOrNew + save: backfillea la columna legacy `color` (la que usa el
            // storefront y ProductController->pluck('color')) en filas ya existentes,
            // SIN pisar el stock/price que se haya ajustado desde el admin.
            $variant = ProductVariant::firstOrNew(
                ['product_id' => $product->id, 'option_type' => 'color', 'value' => $tono],
            );
            $variant->name = 'Tono';
            $variant->color = $tono;       // <- columna que renderiza el swatch
            $variant->color_hex = $hex;
            $variant->is_active = true;
            if (! $variant->exists) {       // solo en creación nueva
                $variant->stock = 10;
                $variant->price_modifier = 0;
            }
            $variant->save();
            $order++;
        }

        $this->command?->info("Jan Beauty: {$order} tonos cargados en el producto #{$product->id}.");
    }
}
