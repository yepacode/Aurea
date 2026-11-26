<?php

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\Migrations\Migration;

/**
 * Carga los 60 tonos del Esmalte Semipermanente Miracle (x15ml) como variantes
 * de color (swatch con color_hex), para que la ficha tenga círculos de color
 * seleccionables igual que el Jan Beauty.
 *
 * Colores: color REAL de cada tono, extraído del splash de la imagen oficial de
 * Miracle (carpeta claudia\belleza-aurea-tonos\miracle). Nombres propios de
 * Miracle; el color es lo que importa (se distribuyen varias marcas).
 *
 * Idempotente (firstOrNew por product_id+value). Stock por defecto 10 en creación
 * (ajustar al inventario real desde el admin -> producto -> variantes); NO pisa
 * stock/precio si la variante ya existe.
 */
return new class extends Migration
{
    public function up(): void
    {
        $product = Product::where('slug', 'esmalte-semipermanente-15ml')->first();
        if (! $product) {
            return; // sin el producto no hay dónde cargar los tonos
        }

        // [tono, hex]
        $colors = [
            ['Abigail', '#FE2523'], ['Adela', '#EB05B2'], ['Agata', '#C7B88E'],
            ['Agustina', '#71C182'], ['Alicia', '#374FA7'], ['Ana', '#D5B4AC'],
            ['Anastasia', '#705C29'], ['Angela', '#FF5C4F'], ['Apolonia', '#48B6FE'],
            ['Aurora', '#1148B4'], ['Beatriz', '#51DAFE'], ['Belén', '#C7B998'],
            ['Berenice', '#FE97C3'], ['Bernarda', '#4F6FC3'], ['Brigida', '#FDBABD'],
            ['Clara', '#EDA8B2'], ['Cristina', '#FF9CAE'], ['Dalila', '#96DCC8'],
            ['Débora', '#D478C8'], ['Edith', '#01E2C5'], ['Elizabeth', '#FD938E'],
            ['Emilia', '#EC0131'], ['Eva', '#9F946B'], ['Faustina', '#CFC4A4'],
            ['Filomena', '#5D459F'], ['Francisca', '#FD907A'], ['Génesis', '#FEBD8C'],
            ['Gianna', '#F9CAA4'], ['Goreti', '#8F4D57'], ['Guadalupe', '#FFBDB3'],
            ['Inés', '#E5B8B0'], ['Irene', '#E7B8A8'], ['Isabel', '#D8C19C'],
            ['Jacinta', '#C4BA9E'], ['Josefina', '#EDC8A4'], ['Juana', '#DF6075'],
            ['Leonor', '#FFB4A7'], ['Lidia', '#FCDCAD'], ['Lila', '#8A74FC'],
            ['Lucia', '#D7365D'], ['Luisa', '#F4A7BC'], ['Magdalena', '#7A7DFE'],
            ['Maria', '#CCC6A9'], ['Matilde', '#F0AB93'], ['Miriam', '#FD568E'],
            ['Nohemi', '#E9A3B2'], ['Priscila', '#98E4A4'], ['Rebeca', '#C1B791'],
            ['Ruth', '#EECEBC'], ['Salome', '#C89EEC'], ['Samara', '#FBBACB'],
            ['Sara', '#F1D69F'], ['Sofía', '#FE95A0'], ['Sonia', '#BEB091'],
            ['Susana', '#E28269'], ['Ursula', '#7F5187'], ['Veronica', '#F6D7AB'],
            ['Victoria', '#6B383B'], ['Virginia', '#FEADD5'], ['Valentina', '#FD9E97'],
        ];

        foreach ($colors as [$tono, $hex]) {
            $v = ProductVariant::firstOrNew([
                'product_id'  => $product->id,
                'option_type' => 'color',
                'value'       => $tono,
            ]);
            $v->name = 'Tono';
            $v->color = $tono;          // columna legacy que renderiza el swatch
            $v->color_hex = $hex;
            $v->is_active = true;
            if (! $v->exists) {
                $v->stock = 10;
                $v->price_modifier = 0;
            }
            $v->save();
        }
    }

    public function down(): void
    {
        $product = Product::where('slug', 'esmalte-semipermanente-15ml')->first();
        if ($product) {
            ProductVariant::where('product_id', $product->id)
                ->where('option_type', 'color')
                ->delete();
        }
    }
};
