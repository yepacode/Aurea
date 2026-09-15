<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FaqCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class FaqCategoryAdminController extends Controller
{
    public function index(): View
    {
        $categories = FaqCategory::withCount('faqs')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('admin.faqs.categories.index', compact('categories'));
    }

    public function create(): View
    {
        return view('admin.faqs.categories.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['slug'] = $this->uniqueSlug($data['name']);

        FaqCategory::create($data);

        return redirect()->route('admin.faq-categories.index')
            ->with('success', 'Categoría "'.$data['name'].'" creada.');
    }

    public function edit(FaqCategory $faq_category): View
    {
        return view('admin.faqs.categories.edit', ['category' => $faq_category]);
    }

    public function update(Request $request, FaqCategory $faq_category): RedirectResponse
    {
        $data = $this->validated($request);
        $data['slug'] = $this->uniqueSlug($data['name'], $faq_category->id);

        $faq_category->update($data);

        return redirect()->route('admin.faq-categories.index')
            ->with('success', 'Categoría actualizada.');
    }

    public function destroy(FaqCategory $faq_category): RedirectResponse
    {
        if ($faq_category->faqs()->exists()) {
            return redirect()->route('admin.faq-categories.index')
                ->with('error', 'No se puede eliminar "'.$faq_category->name.'" porque tiene FAQs asociadas.');
        }

        $name = $faq_category->name;
        $faq_category->delete();

        return redirect()->route('admin.faq-categories.index')
            ->with('success', 'Categoría "'.$name.'" eliminada.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name'       => 'required|string|max:120',
            'emoji'      => 'nullable|string|max:8',
            'sort_order' => 'nullable|integer|min:0',
            'is_active'  => 'nullable|boolean',
        ]) + ['is_active' => (bool) $request->input('is_active', false)];
    }

    private function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name) ?: 'categoria';
        $slug = $base;
        $i = 2;
        while (FaqCategory::where('slug', $slug)->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = $base.'-'.$i;
            $i++;
        }
        return $slug;
    }
}
