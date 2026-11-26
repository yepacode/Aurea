<?php

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Storage;

/**
 * Enlaza cada uno de los 60 tonos del Esmalte Miracle (x15ml) con su imagen
 * propia (columna image_path de product_variants), para que al seleccionar un
 * color en la ficha se muestre la foto de ESE tono (overlay #variant-color-image).
 *
 * Las imagenes viven versionadas en database/seeders/data/tonos_miracle/ (webp).
 * La migracion las copia al disco publico (storage/app/public/tonos/miracle/) y
 * setea image_path = 'tonos/miracle/<archivo>.webp' por nombre de tono.
 *
 * Idempotente: no re-copia si el archivo ya existe; matchea la variante por value.
 */
return new class extends Migration
{
    public function up(): void
    {
        $product = Product::where('slug', 'esmalte-semipermanente-15ml')->first();
        if (! $product) {
            return;
        }

        // [nombre del tono => archivo webp]
        $map = [
            "Abigail" => '1001_abigail.webp',
            "Adela" => '1001_adela.webp',
            "Agata" => '1001_agata.webp',
            "Agustina" => '1001_agustina.webp',
            "Alicia" => '1001_alicia.webp',
            "Ana" => '1001_ana.webp',
            "Anastasia" => '1001_anastasia.webp',
            "Angela" => '1001_angela.webp',
            "Apolonia" => '1001_apolonia.webp',
            "Aurora" => '1001_aurora.webp',
            "Beatriz" => '1001_beatriz.webp',
            "Belén" => '1001_bel-n.webp',
            "Berenice" => '1001_berenice.webp',
            "Bernarda" => '1001_bernarda.webp',
            "Brigida" => '1001_brigida.webp',
            "Clara" => '1001_clara.webp',
            "Cristina" => '1001_cristina.webp',
            "Dalila" => '1001_dalila.webp',
            "Débora" => '1001_d-bora.webp',
            "Edith" => '1001_edith.webp',
            "Elizabeth" => '1001_elizabeth.webp',
            "Emilia" => '1001_emilia.webp',
            "Eva" => '1001_eva.webp',
            "Faustina" => '1001_faustina.webp',
            "Filomena" => '1001_filomena.webp',
            "Francisca" => '1001_francisca.webp',
            "Génesis" => '1001_g-nesis.webp',
            "Gianna" => '1001_gianna.webp',
            "Goreti" => '1001_goreti.webp',
            "Guadalupe" => '1001_guadalupe.webp',
            "Inés" => '1001_in-s.webp',
            "Irene" => '1001_irene.webp',
            "Isabel" => '1001_isabel.webp',
            "Jacinta" => '1001_jacinta.webp',
            "Josefina" => '1001_josefina.webp',
            "Juana" => '1001_juana.webp',
            "Leonor" => '1001_leonor.webp',
            "Lidia" => '1001_lidia.webp',
            "Lila" => '1001_lila.webp',
            "Lucia" => '1001_lucia.webp',
            "Luisa" => '1001_luisa.webp',
            "Magdalena" => '1001_magdalena.webp',
            "Maria" => '1001_maria.webp',
            "Matilde" => '1001_matilde.webp',
            "Miriam" => '1001_miriam.webp',
            "Nohemi" => '1001_nohemi.webp',
            "Priscila" => '1001_priscila.webp',
            "Rebeca" => '1001_rebeca.webp',
            "Ruth" => '1001_ruth.webp',
            "Salome" => '1001_salome.webp',
            "Samara" => '1001_samara.webp',
            "Sara" => '1001_sara.webp',
            "Sofía" => '1001_sof-a.webp',
            "Sonia" => '1001_sonia.webp',
            "Susana" => '1001_susana.webp',
            "Ursula" => '1001_ursula.webp',
            "Valentina" => '1001_valentina.webp',
            "Veronica" => '1001_veronica.webp',
            "Victoria" => '1001_victoria.webp',
            "Virginia" => '1001_virginia.webp',
        ];

        $srcDir  = database_path('seeders/data/tonos_miracle');
        $disk    = Storage::disk('public');
        $destDir = 'tonos/miracle';

        foreach ($map as $name => $file) {
            $srcPath = $srcDir . DIRECTORY_SEPARATOR . $file;
            if (! is_file($srcPath)) {
                continue; // sin la imagen fuente, saltar ese tono
            }

            $destPath = $destDir . '/' . $file;
            if (! $disk->exists($destPath)) {
                $disk->put($destPath, file_get_contents($srcPath));
            }

            ProductVariant::where('product_id', $product->id)
                ->where('option_type', 'color')
                ->where('value', $name)
                ->update(['image_path' => $destPath]);
        }
    }

    public function down(): void
    {
        $product = Product::where('slug', 'esmalte-semipermanente-15ml')->first();
        if ($product) {
            ProductVariant::where('product_id', $product->id)
                ->where('option_type', 'color')
                ->update(['image_path' => null]);
        }
    }
};
