<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request, string $slug): RedirectResponse
    {
        $product = Product::active()->where('slug', $slug)->firstOrFail();

        $data = $request->validate([
            'author_name' => 'required|string|max:80',
            'rating'      => 'required|integer|min:1|max:5',
            'title'       => 'nullable|string|max:120',
            'comment'     => 'nullable|string|max:1500',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('reviews', 'public');
        }

        $product->reviews()->create([
            'author_name' => $data['author_name'],
            'rating'      => $data['rating'],
            'title'       => $data['title'] ?? null,
            'comment'     => $data['comment'] ?? null,
            'image_path'  => $imagePath,
            'is_approved' => false, // moderada antes de publicarse
        ]);

        return redirect()
            ->to(route('products.show', $product->slug) . '#resenas')
            ->with('review_success', '¡Gracias por tu reseña! La publicaremos tras una breve revisión.');
    }
}
