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

        $data['slug'] = Str::slug($data['name']);
        $data['is_featured'] = $request->boolean('is_featured');
        $data['is_active']   = $request->boolean('is_active', true);

        $data = array_merge($data, $this->seoInput($request, 'og_image_path', 'twitter_image_path'));
        unset($data['og_image'], $data['twitter_image']);

        Brand::create($data);

        return redirect()->route('admin.brands.index')->with('success', 'Marca creada.');
    }

    public function edit(Brand $brand): View
    {
        return view('admin.brands.edit', compact('brand'));
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

        $data['slug'] = Str::slug($data['name']);
        $data['is_featured'] = $request->boolean('is_featured');
        $data['is_active']   = $request->boolean('is_active', true);

        $data = array_merge($data, $this->seoInput($request, 'og_image_path', 'twitter_image_path'));
        unset($data['og_image'], $data['twitter_image']);

        $brand->update($data);

        return redirect()->route('admin.brands.index')->with('success', 'Marca actualizada.');
    }

    public function destroy(Brand $brand): RedirectResponse
    {
        if ($brand->logo_path) Storage::disk('public')->delete($brand->logo_path);
        if ($brand->banner_path) Storage::disk('public')->delete($brand->banner_path);
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
        ], $this->seoRules()));
    }
}
