<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Concerns\HandlesSeoInput;
use App\Models\Brand;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BrandAdminController extends Controller
{
    use HandlesSeoInput;

    public function index(): View
    {
        $brands = Brand::withCount('products')->ordered()->paginate(20);
        return view('admin.brands.index', compact('brands'));
    }

    public function create(): View
    {
        return view('admin.brands.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateForm($request);

        if ($request->hasFile('logo')) {
            $data['logo_path'] = $request->file('logo')->store('brands', 'public');
        }
        if ($request->hasFile('banner')) {
            $data['banner_path'] = $request->file('banner')->store('brands', 'public');
        }
        if ($request->hasFile('hero_image')) {
            $data['hero_image'] = $request->file('hero_image')->store('brands', 'public');
        }
        if ($request->hasFile('story_image')) {
            $data['story_image'] = $request->file('story_image')->store('brands', 'public');
        }

        $data['slug'] = Str::slug($data['name']);
        $data['is_featured'] = $request->boolean('is_featured');
        $data['is_active']   = $request->boolean('is_active', true);
        $data['landing_enabled'] = $request->boolean('landing_enabled');

        $data = array_merge($data, $this->normalizeLandingInput($request));
        $data = array_merge($data, $this->seoInput($request, 'og_image_path', 'twitter_image_path'));
        unset($data['og_image'], $data['twitter_image']);

        Brand::create($data);

        return redirect()->route('admin.brands.index')->with('success', 'Marca creada.');
    }

    public function edit(Brand $brand): View
    {
        $brandProducts = \App\Models\Product::where('brand_id', $brand->id)
            ->orderBy('name')
            ->get(['id','name']);
        return view('admin.brands.edit', compact('brand', 'brandProducts'));
    }

    public function update(Request $request, Brand $brand): RedirectResponse
    {
        $data = $this->validateForm($request, $brand->id);

        if ($request->hasFile('logo')) {
            if ($brand->logo_path) Storage::disk('public')->delete($brand->logo_path);
            $data['logo_path'] = $request->file('logo')->store('brands', 'public');
        }
        if ($request->hasFile('banner')) {
            if ($brand->banner_path) Storage::disk('public')->delete($brand->banner_path);
            $data['banner_path'] = $request->file('banner')->store('brands', 'public');
        }
        if ($request->hasFile('hero_image')) {
            if ($brand->hero_image) Storage::disk('public')->delete($brand->hero_image);
            $data['hero_image'] = $request->file('hero_image')->store('brands', 'public');
        }
        if ($request->hasFile('story_image')) {
            if ($brand->story_image) Storage::disk('public')->delete($brand->story_image);
            $data['story_image'] = $request->file('story_image')->store('brands', 'public');
        }

        $data['slug'] = Str::slug($data['name']);
        $data['is_featured'] = $request->boolean('is_featured');
        $data['is_active']   = $request->boolean('is_active', true);
        $data['landing_enabled'] = $request->boolean('landing_enabled');

        $data = array_merge($data, $this->normalizeLandingInput($request));
        $data = array_merge($data, $this->seoInput($request, 'og_image_path', 'twitter_image_path'));
        unset($data['og_image'], $data['twitter_image']);

        $brand->update($data);

        return redirect()->route('admin.brands.index')->with('success', 'Marca actualizada.');
    }

    public function destroy(Brand $brand): RedirectResponse
    {
        if ($brand->logo_path)   Storage::disk('public')->delete($brand->logo_path);
        if ($brand->banner_path) Storage::disk('public')->delete($brand->banner_path);
        if ($brand->hero_image)  Storage::disk('public')->delete($brand->hero_image);
        if ($brand->story_image) Storage::disk('public')->delete($brand->story_image);
        $brand->delete();
        return redirect()->route('admin.brands.index')->with('success', 'Marca eliminada.');
    }

    private function validateForm(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate(array_merge([
            'name' => 'required|string|max:255',
            'short_description' => 'nullable|string|max:255',
            'long_description'  => 'nullable|string',
            'website_url'       => 'nullable|url|max:255',
            'country_origin'    => 'nullable|string|max:100',
            'logo'              => 'nullable|image|mimes:jpg,jpeg,png,webp,svg|max:2048',
            'banner'            => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'sort_order'        => 'nullable|integer|min:0',
            // Landing enriquecida
            'hero_image'    => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'hero_title'    => 'nullable|string|max:255',
            'hero_tagline'  => 'nullable|string|max:500',
            'story_title'   => 'nullable|string|max:255',
            'story_content' => 'nullable|string|max:8000',
            'story_image'   => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'quote_text'    => 'nullable|string|max:1000',
            'quote_author'  => 'nullable|string|max:255',
            'brand_color'   => 'nullable|string|max:9|regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{8})$/',
            // Los arrays de pilares y productos se validan/normalizan a mano
            'pillars'            => 'nullable|array|max:4',
            'pillars.*.icon'     => 'nullable|string|max:16',
            'pillars.*.title'    => 'nullable|string|max:100',
            'pillars.*.description' => 'nullable|string|max:400',
            'featured_products'  => 'nullable|array|max:6',
            'featured_products.*'=> 'nullable|integer',
        ], $this->seoRules()));
    }

    /**
     * Convierte los repeaters/arrays del formulario en JSON limpio para el modelo.
     */
    private function normalizeLandingInput(Request $request): array
    {
        $out = [];

        $pillars = collect($request->input('pillars', []))
            ->filter(function ($p) {
                return is_array($p) && (
                    trim((string)($p['title'] ?? '')) !== '' ||
                    trim((string)($p['description'] ?? '')) !== ''
                );
            })
            ->map(function ($p) {
                return [
                    'icon'        => trim((string)($p['icon'] ?? '')),
                    'title'       => trim((string)($p['title'] ?? '')),
                    'description' => trim((string)($p['description'] ?? '')),
                ];
            })
            ->take(4)
            ->values()
            ->all();
        $out['pillars_json'] = empty($pillars) ? null : $pillars;

        $featured = collect($request->input('featured_products', []))
            ->map(fn ($id) => (int) $id)
            ->filter(fn ($id) => $id > 0)
            ->unique()
            ->take(6)
            ->values()
            ->all();
        $out['featured_products_json'] = empty($featured) ? null : $featured;

        return $out;
    }
}
