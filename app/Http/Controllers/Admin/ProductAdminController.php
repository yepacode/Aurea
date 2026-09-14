<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\HandlesSeoInput;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProductAdminController extends Controller
{
    use HandlesSeoInput;

    public function index(Request $request): View
    {
        $query = Product::with(['category', 'variants']);

        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $products = $query->latest()->paginate(15)->withQueryString();
        $categories = Category::orderBy('sort_order')->get();

        return view('admin.products.index', compact('products', 'categories'));
    }

    public function create(): View
    {
        $categories = Category::orderBy('sort_order')->get();

        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate(array_merge($this->seoRules(), [
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'description' => 'required|string',
            'type' => 'required|array|min:1',
            'type.*' => 'in:miopia,lectura,sin_graduacion,toallitas',
            'price' => 'required|numeric|min:0',
            'compare_price' => 'nullable|numeric|min:0',
            'cost_price'    => 'nullable|numeric|min:0',
            'wholesale_price'   => 'nullable|integer|min:0',
            'wholesale_min_qty' => 'nullable|integer|min:1',
            'stock' => 'required|integer|min:0',
            'slug' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer|min:0',
            'key_features_raw' => 'nullable|string|max:2000',
            'how_to_use' => 'nullable|string|max:5000',
            'ingredients' => 'nullable|string|max:5000',
            'suitable_for' => 'nullable|string|max:500',
            'gtin' => 'nullable|string|max:14',
            'mpn' => 'nullable|string|max:70',
            'weight_value' => 'nullable|numeric|min:0',
            'weight_unit' => 'nullable|in:g,kg,ml,L,oz',
            'country_origin' => 'nullable|string|max:100',
            'is_cruelty_free' => 'boolean',
            'is_vegan' => 'boolean',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'badge_2x1' => 'boolean',
            'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
            'variants.*.option_type' => 'nullable|in:color,size,scent,finish,style,material,quantity,other',
            'variants.*.name' => 'nullable|string|max:100',
            'variants.*.value' => 'nullable|string|max:100',
            'variants.*.color' => 'nullable|string|max:100',
            'variants.*.color_hex' => 'nullable|string|max:7',
            'variants.*.graduation' => 'nullable|string|max:20',
            'variants.*.graduation_type' => 'nullable|in:miopia,lectura,sin_graduacion',
            'variants.*.price_modifier' => 'nullable|numeric',
            'variants.*.stock' => 'nullable|integer|min:0',
            'variants.*.image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]));

        // Slug: si el admin lo personalizó, lo usamos (slugificado y único);
        // si no, lo generamos del nombre.
        $customSlug = trim((string) $request->input('slug', ''));
        $validated['slug'] = $customSlug !== ''
            ? $this->uniqueSlug($customSlug)
            : $this->uniqueSlug($validated['name']);
        $validated['sort_order'] = $request->input('sort_order') ?: 0;
        $validated['is_active'] = $request->boolean('is_active');
        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['badge_2x1'] = $request->boolean('badge_2x1');
        $validated['is_cruelty_free'] = $request->boolean('is_cruelty_free');
        $validated['is_vegan'] = $request->boolean('is_vegan');

        // Precios mayoristas: si vienen vacíos, se guardan como null (wholesale_price)
        // y 1 (wholesale_min_qty) — así el método priceFor() cae al descuento por defecto.
        $validated['wholesale_price']   = $request->filled('wholesale_price') ? (int) $request->input('wholesale_price') : null;
        $validated['wholesale_min_qty'] = max(1, (int) $request->input('wholesale_min_qty', 1));

        // key_features llega como textarea con un bullet por línea
        $rawFeatures = $request->input('key_features_raw', '');
        $features = collect(preg_split('/\r?\n/', $rawFeatures))
            ->map(fn ($l) => trim($l))
            ->filter()
            ->values()
            ->all();
        $validated['key_features'] = ! empty($features) ? $features : null;
        unset($validated['key_features_raw']);

        // SEO por-ítem (meta, keywords, canónica, robots, OG, Twitter, JSON-LD) — trait compartido
        $validated = array_merge($validated, $this->seoInput($request));
        unset($validated['og_image'], $validated['twitter_image']);

        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imagePaths[] = $image->store('products', 'public');
            }
        }
        $validated['images'] = $imagePaths;

        $product = Product::create($validated);

        if ($request->has('variants')) {
            foreach ($request->variants as $index => $variant) {
                if (! empty($variant['name']) && ! empty($variant['value'])) {
                    $imagePath = null;
                    if ($request->hasFile("variants.$index.image")) {
                        $imagePath = $request->file("variants.$index.image")->store('variants', 'public');
                    }
                    $product->variants()->create([
                        'option_type' => $variant['option_type'] ?? 'other',
                        'name' => $variant['name'],
                        'value' => $variant['value'],
                        'color' => ($variant['option_type'] ?? null) === 'color'
                            ? ($variant['color'] ?? $variant['value'])
                            : ($variant['color'] ?? null),
                        'color_hex' => ! empty($variant['color_hex']) ? $variant['color_hex'] : null,
                        'graduation' => ! empty($variant['graduation']) ? $variant['graduation'] : null,
                        'graduation_type' => ! empty($variant['graduation_type']) ? $variant['graduation_type'] : null,
                        'price_modifier' => $variant['price_modifier'] ?? 0,
                        'stock' => $variant['stock'] ?? 0,
                        'image_path' => $imagePath,
                        'is_active' => true,
                    ]);
                }
            }
        }

        return redirect()->route('admin.products.index')
            ->with('success', 'Producto creado exitosamente.');
    }

    public function show(Product $product): View
    {
        $product->load('category', 'variants');

        return view('admin.products.show', compact('product'));
    }

    public function edit(Product $product): View
    {
        $product->load('variants');
        $categories = Category::orderBy('sort_order')->get();

        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate(array_merge($this->seoRules(), [
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'description' => 'required|string',
            'type' => 'required|array|min:1',
            'type.*' => 'in:miopia,lectura,sin_graduacion,toallitas',
            'price' => 'required|numeric|min:0',
            'compare_price' => 'nullable|numeric|min:0',
            'cost_price'    => 'nullable|numeric|min:0',
            'wholesale_price'   => 'nullable|integer|min:0',
            'wholesale_min_qty' => 'nullable|integer|min:1',
            'stock' => 'required|integer|min:0',
            'slug' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer|min:0',
            'key_features_raw' => 'nullable|string|max:2000',
            'how_to_use' => 'nullable|string|max:5000',
            'ingredients' => 'nullable|string|max:5000',
            'suitable_for' => 'nullable|string|max:500',
            'gtin' => 'nullable|string|max:14',
            'mpn' => 'nullable|string|max:70',
            'weight_value' => 'nullable|numeric|min:0',
            'weight_unit' => 'nullable|in:g,kg,ml,L,oz',
            'country_origin' => 'nullable|string|max:100',
            'is_cruelty_free' => 'boolean',
            'is_vegan' => 'boolean',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'badge_2x1' => 'boolean',
            'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
            'remove_images' => 'nullable|array',
            'variants.*.id' => 'nullable|integer',
            'variants.*.option_type' => 'nullable|in:color,size,scent,finish,style,material,quantity,other',
            'variants.*.name' => 'nullable|string|max:100',
            'variants.*.value' => 'nullable|string|max:100',
            'variants.*.color' => 'nullable|string|max:100',
            'variants.*.color_hex' => 'nullable|string|max:7',
            'variants.*.graduation' => 'nullable|string|max:20',
            'variants.*.graduation_type' => 'nullable|in:miopia,lectura,sin_graduacion',
            'variants.*.price_modifier' => 'nullable|numeric',
            'variants.*.stock' => 'nullable|integer|min:0',
            'variants.*.image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'variants.*.remove_image' => 'nullable|boolean',
        ]));

        $customSlug = trim((string) $request->input('slug', ''));
        $validated['slug'] = $customSlug !== ''
            ? $this->uniqueSlug($customSlug, $product->id)
            : $this->uniqueSlug($validated['name'], $product->id);
        $validated['sort_order'] = $request->input('sort_order') ?: 0;
        $validated['is_active'] = $request->boolean('is_active');
        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['badge_2x1'] = $request->boolean('badge_2x1');
        $validated['is_cruelty_free'] = $request->boolean('is_cruelty_free');
        $validated['is_vegan'] = $request->boolean('is_vegan');

        // Precios mayoristas — misma normalización que en store().
        $validated['wholesale_price']   = $request->filled('wholesale_price') ? (int) $request->input('wholesale_price') : null;
        $validated['wholesale_min_qty'] = max(1, (int) $request->input('wholesale_min_qty', 1));

        // key_features llega como textarea con un bullet por línea
        $rawFeatures = $request->input('key_features_raw', '');
        $features = collect(preg_split('/\r?\n/', $rawFeatures))
            ->map(fn ($l) => trim($l))
            ->filter()
            ->values()
            ->all();
        $validated['key_features'] = ! empty($features) ? $features : null;
        unset($validated['key_features_raw']);

        // SEO por-ítem (meta, keywords, canónica, robots, OG, Twitter, JSON-LD) — trait compartido
        $validated = array_merge($validated, $this->seoInput($request));
        unset($validated['og_image'], $validated['twitter_image']);

        // Handle image removal
        $currentImages = $product->images ?? [];
        if ($request->has('remove_images')) {
            foreach ($request->remove_images as $imagePath) {
                Storage::disk('public')->delete($imagePath);
                $currentImages = array_values(array_diff($currentImages, [$imagePath]));
            }
        }

        // Handle new image uploads
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $currentImages[] = $image->store('products', 'public');
            }
        }
        $validated['images'] = $currentImages;

        unset($validated['remove_images']);
        $product->update($validated);

        // Sync variants
        if ($request->has('variants')) {
            $keepIds = [];
            foreach ($request->variants as $index => $variant) {
                if (! empty($variant['name']) && ! empty($variant['value'])) {
                    $payload = [
                        'option_type' => $variant['option_type'] ?? 'other',
                        'name' => $variant['name'],
                        'value' => $variant['value'],
                        'color' => ($variant['option_type'] ?? null) === 'color'
                            ? ($variant['color'] ?? $variant['value'])
                            : ($variant['color'] ?? null),
                        'color_hex' => ! empty($variant['color_hex']) ? $variant['color_hex'] : null,
                        'graduation' => ! empty($variant['graduation']) ? $variant['graduation'] : null,
                        'graduation_type' => ! empty($variant['graduation_type']) ? $variant['graduation_type'] : null,
                        'price_modifier' => $variant['price_modifier'] ?? 0,
                        'stock' => $variant['stock'] ?? 0,
                        'is_active' => true,
                    ];

                    if (! empty($variant['id'])) {
                        $existing = $product->variants()->find($variant['id']);
                        if ($existing) {
                            if (! empty($variant['remove_image']) && $existing->image_path) {
                                Storage::disk('public')->delete($existing->image_path);
                                $payload['image_path'] = null;
                            }
                            if ($request->hasFile("variants.$index.image")) {
                                if ($existing->image_path) {
                                    Storage::disk('public')->delete($existing->image_path);
                                }
                                $payload['image_path'] = $request->file("variants.$index.image")->store('variants', 'public');
                            }
                            $existing->update($payload);
                            $keepIds[] = $existing->id;
                        }
                    } else {
                        if ($request->hasFile("variants.$index.image")) {
                            $payload['image_path'] = $request->file("variants.$index.image")->store('variants', 'public');
                        }
                        $newVariant = $product->variants()->create($payload);
                        $keepIds[] = $newVariant->id;
                    }
                }
            }
            $removed = $product->variants()->whereNotIn('id', $keepIds)->get();
            foreach ($removed as $r) {
                if ($r->image_path) {
                    Storage::disk('public')->delete($r->image_path);
                }
                $r->delete();
            }
        } else {
            foreach ($product->variants as $v) {
                if ($v->image_path) {
                    Storage::disk('public')->delete($v->image_path);
                }
            }
            $product->variants()->delete();
        }

        return redirect()->route('admin.products.index')
            ->with('success', 'Producto actualizado exitosamente.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        if ($product->images) {
            foreach ($product->images as $image) {
                Storage::disk('public')->delete($image);
            }
        }

        foreach ($product->variants as $v) {
            if ($v->image_path) {
                Storage::disk('public')->delete($v->image_path);
            }
        }
        $product->variants()->delete();
        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Producto eliminado.');
    }

    private function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $i = 2;
        while (Product::where('slug', $slug)->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }

    public function toggle(Product $product): RedirectResponse
    {
        $product->update(['is_active' => ! $product->is_active]);

        $status = $product->is_active ? 'activado' : 'desactivado';

        return redirect()->route('admin.products.index')
            ->with('success', "Producto {$status}.");
    }
}
