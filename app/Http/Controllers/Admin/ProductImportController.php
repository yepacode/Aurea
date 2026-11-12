<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;

/**
 * Importador masivo de productos desde Excel.
 *
 * Estructura esperada (orden de columnas o por header, lo que aparezca primero):
 *   1. Referencia                  → internal_code
 *   2. Nombre                      → name (slug auto)
 *   3. Categoría                   → categoría (find or create)
 *   4. Descripción                 → description ('-' o vacío = sin descripción)
 *   5. PV CENTRO DE EXP (opcional) → compare_price (precio "tachado")
 *   6. PV DISTRIBUIDOR (opcional)  → guardado en variante interna (no se muestra)
 *   7. Venta                       → price (precio público)
 *
 * Productos sin "Venta" se omiten, salvo que se marque "fallback PV" en el form.
 * Productos sin "Nombre" o sin "Referencia" se omiten siempre.
 */
class ProductImportController extends Controller
{
    /**
     * Muestra el formulario.
     */
    public function show()
    {
        return view('admin.products.import');
    }

    /**
     * Procesa el archivo subido.
     */
    public function store(Request $request): RedirectResponse
    {
        // Subir límites de PHP en runtime — algunos exports de Excel traen
        // imágenes embebidas y pesan 100+ MB.
        @ini_set('upload_max_filesize', '200M');
        @ini_set('post_max_size', '200M');
        @ini_set('memory_limit', '1024M');
        @ini_set('max_execution_time', '600');
        set_time_limit(600);

        $validated = $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:204800', // 200 MB
            'default_type' => 'required|string|in:sin_graduacion,toallitas',
            'default_stock' => 'required|integer|min:0',
            'fallback_pv_when_no_venta' => 'sometimes|boolean',
            'mark_active' => 'sometimes|boolean',
        ]);

        $path = $request->file('file')->getRealPath();

        try {
            $reader = IOFactory::createReaderForFile($path);
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($path);
            $rows = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
        } catch (\Throwable $e) {
            return back()->with('error', 'No se pudo leer el archivo: '.$e->getMessage());
        }

        // Detectar encabezados (primera fila no vacía con texto)
        $headerRow = null;
        $headerMap = [];
        foreach ($rows as $index => $row) {
            $values = array_filter(array_map(fn ($v) => is_string($v) ? trim($v) : $v, $row));
            if (count($values) >= 3) {
                $headerRow = $index;
                foreach ($row as $col => $value) {
                    $key = $this->normalizeHeader((string) $value);
                    if ($key) {
                        $headerMap[$key] = $col;
                    }
                }
                break;
            }
        }

        if (! $headerRow) {
            return back()->with('error', 'El archivo no tiene encabezados reconocibles.');
        }

        // Columnas mínimas requeridas
        if (! isset($headerMap['nombre']) || ! isset($headerMap['referencia'])) {
            return back()->with('error', 'Faltan columnas obligatorias: "Referencia" y "Nombre".');
        }

        $stats = [
            'created' => 0,
            'updated' => 0,
            'skipped' => 0,
            'categories_created' => 0,
            'brands_created' => 0,
            'errors' => [],
        ];

        // Recorrer filas de datos (después del header)
        foreach ($rows as $index => $row) {
            if ($index <= $headerRow) {
                continue;
            }

            try {
                $refRaw = $this->cell($row, $headerMap, 'referencia');
                $name = trim((string) $this->cell($row, $headerMap, 'nombre'));

                if ($name === '' || $refRaw === null || trim((string) $refRaw) === '') {
                    $stats['skipped']++;
                    continue;
                }

                $internalCode = trim((string) $refRaw);

                $categoryName = trim((string) $this->cell($row, $headerMap, 'categoria'));
                $brandName    = trim((string) $this->cell($row, $headerMap, 'marca'));
                $description  = trim((string) $this->cell($row, $headerMap, 'descripcion'));
                $description  = ($description === '-' || $description === '') ? null : $description;

                $pvCentro       = $this->parsePrice($this->cell($row, $headerMap, 'pv_centro'));
                $pvDistribuidor = $this->parsePrice($this->cell($row, $headerMap, 'pv_distribuidor'));
                $venta          = $this->parsePrice($this->cell($row, $headerMap, 'venta'));

                // cost_price = PV Distribuidor (mi costo)
                $costPrice = $pvDistribuidor;

                // Precio final: Venta. Si falta y el admin marcó fallback, usa PV centro.
                $price = $venta;
                if ($price === null && ! empty($validated['fallback_pv_when_no_venta']) && $pvCentro !== null) {
                    $price = $pvCentro;
                }

                if ($price === null) {
                    $stats['skipped']++;
                    $stats['errors'][] = "Fila $index: $name — sin precio de venta, omitido.";
                    continue;
                }

                // compare_price solo si es mayor que el precio
                $comparePrice = ($pvCentro && $pvCentro > $price) ? $pvCentro : null;

                // Categoría: find-or-create por slug
                $category = null;
                if ($categoryName !== '') {
                    $category = $this->findOrCreateCategory($categoryName, $stats);
                }

                // Marca: find-or-create por slug
                $brand = null;
                if ($brandName !== '') {
                    $brand = $this->findOrCreateBrand($brandName, $stats);
                }

                $product = Product::firstOrNew(['internal_code' => $internalCode]);

                $isNew = ! $product->exists;

                $product->fill([
                    'category_id'  => $category?->id ?? $product->category_id,
                    'brand_id'     => $brand?->id ?? $product->brand_id,
                    'name'         => $name,
                    'slug'         => $product->slug ?: Str::slug($name).'-'.Str::lower($internalCode),
                    'description'  => $description ?? ($product->description ?? $name),
                    'type'         => $product->type ?: [$validated['default_type']],
                    'price'        => $price,
                    'compare_price' => $comparePrice,
                    'cost_price'   => $costPrice,
                    'stock'        => $isNew ? $validated['default_stock'] : $product->stock,
                    'is_active'    => $validated['mark_active'] ?? true,
                ]);

                if (! $product->images) {
                    $product->images = [];
                }

                $product->save();

                $stats[$isNew ? 'created' : 'updated']++;
            } catch (\Throwable $e) {
                $stats['skipped']++;
                $stats['errors'][] = "Fila $index: ".$e->getMessage();
                Log::warning('Product import row failed', ['row' => $index, 'error' => $e->getMessage()]);
            }
        }

        // Construir mensaje resultado
        $msg = sprintf(
            'Importación lista: %d creados, %d actualizados, %d categorías nuevas, %d marcas nuevas, %d omitidos.',
            $stats['created'],
            $stats['updated'],
            $stats['categories_created'],
            $stats['brands_created'] ?? 0,
            $stats['skipped'],
        );

        if (! empty($stats['errors'])) {
            session()->flash('import_errors', array_slice($stats['errors'], 0, 30));
        }

        return redirect()->route('admin.products.index')->with('success', $msg);
    }

    /**
     * Encuentra o crea una marca por nombre.
     */
    private function findOrCreateBrand(string $name, array &$stats): \App\Models\Brand
    {
        $slug = Str::slug($name);

        $brand = \App\Models\Brand::where('slug', $slug)->orWhere('name', $name)->first();
        if ($brand) {
            return $brand;
        }

        $brand = \App\Models\Brand::create([
            'name' => $name,
            'slug' => $slug,
            'is_active'   => true,
            'is_featured' => true,
            'sort_order'  => (\App\Models\Brand::max('sort_order') ?? 0) + 1,
        ]);

        $stats['brands_created'] = ($stats['brands_created'] ?? 0) + 1;
        return $brand;
    }

    /**
     * Encuentra o crea una categoría por nombre.
     */
    private function findOrCreateCategory(string $name, array &$stats): Category
    {
        $slug = Str::slug($name);

        $category = Category::where('slug', $slug)
            ->orWhere('name', $name)
            ->first();

        if ($category) {
            return $category;
        }

        $category = Category::create([
            'name'        => $name,
            'slug'        => $slug,
            'description' => null,
            'is_active'   => true,
            'sort_order'  => (Category::max('sort_order') ?? 0) + 1,
        ]);

        $stats['categories_created']++;

        return $category;
    }

    /**
     * Mapea cualquier variante de encabezado a una clave canónica.
     */
    private function normalizeHeader(string $raw): ?string
    {
        $key = Str::lower($raw);
        $key = preg_replace('/[áä]/u', 'a', $key);
        $key = preg_replace('/[éë]/u', 'e', $key);
        $key = preg_replace('/[íï]/u', 'i', $key);
        $key = preg_replace('/[óö]/u', 'o', $key);
        $key = preg_replace('/[úü]/u', 'u', $key);
        $key = preg_replace('/ñ/u', 'n', $key);
        $key = preg_replace('/[^a-z0-9 ]/', '', $key);
        $key = trim(preg_replace('/\s+/', ' ', $key));

        return match (true) {
            $key === 'referencia' || $key === 'ref' || $key === 'codigo' || $key === 'sku' => 'referencia',
            $key === 'nombre' || $key === 'producto' || $key === 'descripcion del producto' => 'nombre',
            $key === 'categoria' || $key === 'categoria del producto' => 'categoria',
            $key === 'marca' || $key === 'brand' || $key === 'fabricante' => 'marca',
            $key === 'descripcion' || $key === 'detalle' => 'descripcion',
            str_contains($key, 'pv centro') || str_contains($key, 'precio centro')
                || str_contains($key, 'precio anterior') || str_contains($key, 'pvp') => 'pv_centro',
            str_contains($key, 'pv distribuidor') || str_contains($key, 'precio distribuidor')
                || str_contains($key, 'costo') => 'pv_distribuidor',
            $key === 'venta' || $key === 'precio' || $key === 'precio publico'
                || str_contains($key, 'precio venta') => 'venta',
            default => null,
        };
    }

    private function cell(array $row, array $headerMap, string $key)
    {
        if (! isset($headerMap[$key])) {
            return null;
        }

        return $row[$headerMap[$key]] ?? null;
    }

    /**
     * Parsea precios en formato "10,000.00" o "10.000,00" o numérico.
     */
    private function parsePrice($value): ?float
    {
        if ($value === null || $value === '' || $value === '-') {
            return null;
        }

        if (is_numeric($value)) {
            return (float) $value;
        }

        $clean = trim((string) $value);

        // Eliminar símbolos y espacios
        $clean = preg_replace('/[^0-9,.\-]/', '', $clean);

        if ($clean === '' || $clean === '-') {
            return null;
        }

        // Si tiene coma Y punto, asumimos formato 10,000.00 (coma miles, punto decimal)
        if (str_contains($clean, ',') && str_contains($clean, '.')) {
            // Si la coma está antes que el punto → coma es miles
            if (strpos($clean, ',') < strpos($clean, '.')) {
                $clean = str_replace(',', '', $clean);
            } else {
                // Formato europeo: punto miles, coma decimal
                $clean = str_replace('.', '', $clean);
                $clean = str_replace(',', '.', $clean);
            }
        } elseif (str_contains($clean, ',')) {
            // Solo coma. Si después de la última coma hay 1-2 dígitos, es decimal. Si son 3, es miles.
            $parts = explode(',', $clean);
            $last = end($parts);

            if (count($parts) === 2 && strlen($last) <= 2) {
                $clean = str_replace(',', '.', $clean);
            } else {
                $clean = str_replace(',', '', $clean);
            }
        }

        return is_numeric($clean) ? (float) $clean : null;
    }
}
