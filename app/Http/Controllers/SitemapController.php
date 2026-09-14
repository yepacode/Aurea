<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $products = Product::active()
            ->select('slug', 'name', 'images', 'updated_at')
            ->orderByDesc('updated_at')
            ->get();

        $posts = BlogPost::published()
            ->select('slug', 'updated_at', 'published_at')
            ->orderByDesc('published_at')
            ->get();

        $categories = Category::select('slug', 'updated_at')->get();

        $brands = Brand::active()
            ->select('slug', 'updated_at')
            ->orderBy('name')
            ->get();

        $xml = view('sitemap', compact('products', 'posts', 'categories', 'brands'))->render();

        return response($xml, 200, [
            'Content-Type' => 'application/xml; charset=utf-8',
        ]);
    }
}
