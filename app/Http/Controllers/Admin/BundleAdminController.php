<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bundle;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BundleAdminController extends Controller
{
    public function index(): View
    {
        $bundles = Bundle::withCount('items')
            ->orderBy('sort_order')
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('admin.bundles.index', compact('bundles'));
    }

    public function create(): View
    {
        $bundle = new Bundle();
        $products = Product::active()->orderBy('name')->get(['id', 'name', 'price']);
        $selected = [];
        return view('admin.bundles.edit', compact('bundle', 'products', 'selected'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateForm($request);

        $data['slug'] = $this->uniqueSlug($data['name']);
        $data['is_active'] = $request->boolean('is_active', true);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('bundles', 'public');
        }

        $bundle = Bundle::create($data);
        $this->syncItems($bundle, $request);

        return redirect()->route('admin.bundles.edit', $bundle)
            ->with('success', 'Kit creado.');
    }

    public function edit(Bundle $bundle): View
    {
        $bundle->load('items');
        $products = Product::active()->orderBy('name')->get(['id', 'name', 'price']);
        $selected = $bundle->items->map(fn ($p) => [
            'product_id' => $p->id,
            'quantity'   => (int) $p->pivot->quantity,
            'sort_order' => (int) $p->pivot->sort_order,
        ])->values()->all();
        return view('admin.bundles.edit', compact('bundle', 'products', 'selected'));
    }

    public function update(Request $request, Bundle $bundle): RedirectResponse
    {
        $data = $this->validateForm($request);

        if ($data['name'] !== $bundle->name || $request->filled('slug_regenerate')) {
            $data['slug'] = $this->uniqueSlug($data['name'], $bundle->id);
        }
        $data['is_active'] = $request->boolean('is_active', true);

        if ($request->hasFile('image')) {
            if ($bundle->image) Storage::disk('public')->delete($bundle->image);
            $data['image'] = $request->file('image')->store('bundles', 'public');
        }
        if ($request->boolean('remove_image') && $bundle->image) {
            Storage::disk('public')->delete($bundle->image);
            $data['image'] = null;
        }

        $bundle->update($data);
        $this->syncItems($bundle, $request);

        return redirect()->route('admin.bundles.edit', $bundle)
            ->with('success', 'Kit actualizado.');
    }

    public function destroy(Bundle $bundle): RedirectResponse
    {
        if ($bundle->image) Storage::disk('public')->delete($bundle->image);
        $bundle->delete();
        return redirect()->route('admin.bundles.index')->with('success', 'Kit eliminado.');
    }

    public function toggle(Bundle $bundle): RedirectResponse
    {
        $bundle->update(['is_active' => ! $bundle->is_active]);
        return back()->with('success', $bundle->is_active ? 'Kit activado.' : 'Kit desactivado.');
    }

    private function validateForm(Request $request): array
    {
        return $request->validate([
            'name'          => 'required|string|max:255',
            'description'   => 'nullable|string|max:2000',
            'image'         => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'price'         => 'required|integer|min:0',
            'compare_price' => 'required|integer|min:0',
            'sort_order'    => 'nullable|integer|min:0',
            'starts_at'     => 'nullable|date',
            'ends_at'       => 'nullable|date|after_or_equal:starts_at',
        ]);
    }

    /**
     * Sincroniza los productos del kit. Los datos vienen como arrays paralelos:
     *   items[]           → product_id
     *   quantities[]      → cantidad (>=1)
     *   sort_orders[]     → orden (opcional)
     */
    private function syncItems(Bundle $bundle, Request $request): void
    {
        $ids   = (array) $request->input('items', []);
        $qtys  = (array) $request->input('quantities', []);
        $sorts = (array) $request->input('sort_orders', []);

        $sync = [];
        foreach ($ids as $i => $pid) {
            $pid = (int) $pid;
            if ($pid <= 0) continue;
            $qty = max(1, (int) ($qtys[$i] ?? 1));
            $so  = (int) ($sorts[$i] ?? $i);
            $sync[$pid] = ['quantity' => $qty, 'sort_order' => $so];
        }

        $bundle->items()->sync($sync);
    }

    private function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name);
        $slug = $base ?: 'kit';
        $i = 2;
        while (Bundle::where('slug', $slug)->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = $base.'-'.$i++;
        }
        return $slug;
    }
}
