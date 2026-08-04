<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\HandlesSeoInput;
use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CategoryAdminController extends Controller
{
    use HandlesSeoInput;

    public function index(): View
    {
        $categories = Category::withCount('products')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('admin.categories.index', compact('categories'));
    }

    public function create(): View
    {
        return view('admin.categories.create');
    }

    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $this->validateForm($request);
        $validated['slug'] = Str::slug($validated['name']);
        $validated['sort_order'] = $validated['sort_order'] ?? (Category::max('sort_order') + 1);
        // type_filter legacy: queda null por defecto. La clasificación se hace
        // desde el producto, no desde la categoría.
        $validated['type_filter'] = null;

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('categories', 'public');
        }

        $validated = array_merge($validated, $this->seoInput($request, 'og_image_path', 'twitter_image_path'));
        unset($validated['og_image'], $validated['twitter_image']);

        $category = Category::create($validated);

        // AJAX desde quick-create dropdown en product form
        if ($request->expectsJson()) {
            return response()->json([
                'id'   => $category->id,
                'name' => $category->name,
            ]);
        }

        return redirect()->route('admin.categories.index')
            ->with('success', 'Categoría "'.$category->name.'" creada.');
    }

    public function edit(Category $category): View
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $validated = $this->validateForm($request, $category->id);
        $validated['slug'] = Str::slug($validated['name']);
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        if ($request->hasFile('image')) {
            if ($category->image) {
                Storage::disk('public')->delete($category->image);
            }
            $validated['image'] = $request->file('image')->store('categories', 'public');
        }

        if ($request->boolean('remove_image') && $category->image) {
            Storage::disk('public')->delete($category->image);
            $validated['image'] = null;
        }

        $validated = array_merge($validated, $this->seoInput($request, 'og_image_path', 'twitter_image_path'));
        unset($validated['og_image'], $validated['twitter_image']);

        $category->update($validated);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Categoría actualizada.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        if ($category->products()->exists()) {
            return redirect()->route('admin.categories.index')
                ->with('error', 'No se puede eliminar "'.$category->name.'" porque tiene productos asociados. Reasigna o elimina esos productos primero.');
        }

        if ($category->image) {
            Storage::disk('public')->delete($category->image);
        }

        $name = $category->name;
        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', 'Categoría "'.$name.'" eliminada.');
    }

    private function validateForm(Request $request, ?int $ignoreId = null): array
    {
        $uniqueRule = 'unique:categories,name'.($ignoreId ? ','.$ignoreId : '');
        return $request->validate(array_merge([
            'name'        => 'required|string|max:255|'.$uniqueRule,
            'description' => 'nullable|string|max:1000',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'sort_order'  => 'nullable|integer|min:0',
            'promo_2x1'   => 'nullable|boolean',
        ], $this->seoRules()));
    }
}
