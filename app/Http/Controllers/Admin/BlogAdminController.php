<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\HandlesSeoInput;
use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BlogAdminController extends Controller
{
    use HandlesSeoInput;

    public function index(): View
    {
        $posts = BlogPost::latest()->paginate(15);

        return view('admin.blog.index', compact('posts'));
    }

    public function create(): View
    {
        return view('admin.blog.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate(array_merge([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'excerpt' => 'nullable|string|max:500',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'featured_image_alt' => 'nullable|string|max:255',
            'category' => 'nullable|in:skincare,unas,maquillaje,cabello,rituales',
            'status' => 'required|in:draft,published,archived',
            'published_at' => 'nullable|date',
            'author_name' => 'nullable|string|max:255',
            'schema_type' => 'nullable|in:Article,BlogPosting,NewsArticle',
        ], $this->seoRules()));

        $validated['slug'] = Str::slug($validated['title']);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('blog', 'public');
        }

        // Auto-set published_at if publishing and no date set
        if ($validated['status'] === 'published' && empty($validated['published_at'])) {
            $validated['published_at'] = now();
        }

        $validated['author_name'] = ($validated['author_name'] ?? null) ?: 'Belleza Áurea';
        $validated['schema_type'] = ($validated['schema_type'] ?? null) ?: 'BlogPosting';

        // Full SEO panel: og image saves to the `og_image` column for blog posts.
        $validated = array_merge($validated, $this->seoInput($request, 'og_image', 'twitter_image_path'));
        unset($validated['twitter_image']);

        BlogPost::create($validated);

        return redirect()->route('admin.blog.index')
            ->with('success', 'Artículo creado.');
    }

    public function show(BlogPost $blog): View
    {
        return view('admin.blog.show', ['post' => $blog]);
    }

    public function edit(BlogPost $blog): View
    {
        return view('admin.blog.edit', ['post' => $blog]);
    }

    public function update(Request $request, BlogPost $blog): RedirectResponse
    {
        $validated = $request->validate(array_merge([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'excerpt' => 'nullable|string|max:500',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'featured_image_alt' => 'nullable|string|max:255',
            'category' => 'nullable|in:skincare,unas,maquillaje,cabello,rituales',
            'status' => 'required|in:draft,published,archived',
            'published_at' => 'nullable|date',
            'author_name' => 'nullable|string|max:255',
            'schema_type' => 'nullable|in:Article,BlogPosting,NewsArticle',
        ], $this->seoRules()));

        $validated['slug'] = Str::slug($validated['title']);

        if ($request->hasFile('image')) {
            if ($blog->image) {
                Storage::disk('public')->delete($blog->image);
            }
            $validated['image'] = $request->file('image')->store('blog', 'public');
        }

        // Auto-set published_at if publishing for the first time
        if ($validated['status'] === 'published' && empty($validated['published_at']) && ! $blog->published_at) {
            $validated['published_at'] = now();
        }

        $validated['author_name'] = ($validated['author_name'] ?? null) ?: 'Belleza Áurea';
        $validated['schema_type'] = ($validated['schema_type'] ?? null) ?: 'BlogPosting';

        // Full SEO panel: og image saves to the `og_image` column for blog posts.
        $validated = array_merge($validated, $this->seoInput($request, 'og_image', 'twitter_image_path'));
        unset($validated['twitter_image']);

        $blog->update($validated);

        return redirect()->route('admin.blog.index')
            ->with('success', 'Artículo actualizado.');
    }

    public function destroy(BlogPost $blog): RedirectResponse
    {
        if ($blog->image) {
            Storage::disk('public')->delete($blog->image);
        }

        $blog->delete();

        return redirect()->route('admin.blog.index')
            ->with('success', 'Artículo eliminado.');
    }
}
