<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

class FeedController extends Controller
{
    /**
     * Google Merchant Center product feed (RSS 2.0 + g: namespace).
     *
     * Feed público que el cliente sube al Merchant Center para que sus
     * productos aparezcan en Google Shopping.
     */
    public function googleMerchant(): Response
    {
        $xml = Cache::remember('google_merchant_feed', 3600, function () {
            $products = Product::query()
                ->where('is_active', true)
                ->where('stock', '>', 0)
                ->with(['category', 'brand'])
                ->orderBy('id')
                ->get();

            return view('feeds.google-merchant', compact('products'))->render();
        });

        return response($xml, 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
        ]);
    }

    /**
     * Feed de catálogo para Meta (Facebook/Instagram Shops) y anuncios dinámicos.
     *
     * Meta Commerce Manager acepta el mismo formato RSS 2.0 + namespace g: que
     * Google, así que reutilizamos la vista del feed de Google. La diferencia:
     * aquí EXCLUIMOS los productos sin foto propia — en el feed de Google esos
     * salen con el logo como imagen, pero en una tienda de Meta eso se ve mal y
     * puede provocar el rechazo en la revisión.
     */
    public function metaCatalog(): Response
    {
        $xml = Cache::remember('meta_catalog_feed', 3600, function () {
            $products = Product::query()
                ->where('is_active', true)
                ->where('stock', '>', 0)
                ->with(['category', 'brand'])
                ->orderBy('id')
                ->get()
                // Solo productos con al menos una imagen propia (no el placeholder).
                ->filter(fn (Product $p) => ! empty($p->images))
                ->values();

            return view('feeds.google-merchant', compact('products'))->render();
        });

        return response($xml, 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
        ]);
    }
}
