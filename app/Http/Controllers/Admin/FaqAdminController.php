<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use App\Models\FaqCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FaqAdminController extends Controller
{
    public function index(Request $request): View
    {
        $categories = FaqCategory::orderBy('sort_order')->orderBy('name')->get();

        $categoryId = $request->integer('category');
        $categoryId = $categoryId > 0 ? $categoryId : null;

        $faqs = Faq::query()
            ->with('category:id,name,slug,emoji')
            ->when($categoryId, fn ($q) => $q->where('faq_category_id', $categoryId))
            ->orderBy('faq_category_id')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return view('admin.faqs.index', [
            'faqs'       => $faqs,
            'categories' => $categories,
            'categoryId' => $categoryId,
        ]);
    }

    public function create(): View
    {
        $categories = FaqCategory::orderBy('sort_order')->orderBy('name')->get();

        if ($categories->isEmpty()) {
            return view('admin.faqs.no-categories');
        }

        return view('admin.faqs.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        Faq::create($data);

        return redirect()->route('admin.faqs.index', ['category' => $data['faq_category_id']])
            ->with('success', 'FAQ creada.');
    }

    public function edit(Faq $faq): View
    {
        $categories = FaqCategory::orderBy('sort_order')->orderBy('name')->get();
        return view('admin.faqs.edit', compact('faq', 'categories'));
    }

    public function update(Request $request, Faq $faq): RedirectResponse
    {
        $data = $this->validated($request);
        $faq->update($data);

        return redirect()->route('admin.faqs.index', ['category' => $faq->faq_category_id])
            ->with('success', 'FAQ actualizada.');
    }

    public function destroy(Faq $faq): RedirectResponse
    {
        $categoryId = $faq->faq_category_id;
        $faq->delete();

        return redirect()->route('admin.faqs.index', ['category' => $categoryId])
            ->with('success', 'FAQ eliminada.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'faq_category_id' => 'required|integer|exists:faq_categories,id',
            'question'        => 'required|string|max:255',
            'answer'          => 'required|string|max:8000',
            'sort_order'      => 'nullable|integer|min:0',
            'is_active'       => 'nullable|boolean',
        ]) + ['is_active' => (bool) $request->input('is_active', false)];
    }
}
