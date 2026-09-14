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
}
