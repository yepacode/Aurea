<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class WishlistController extends Controller
{
    private function customer()
    {
        return Auth::guard('customer')->user();
    }

    public function index(): View
    {
        $customer = $this->customer();
        $products = $customer->wishlist()->with('brand')->latest('wishlist_items.created_at')->get();

        return view('account.wishlist', compact('customer', 'products'));
    }

    /**
     * Agrega o quita un producto de la wishlist. Devuelve el estado nuevo.
     */
    public function toggle(Request $request): JsonResponse
    {
        $data = $request->validate([
            'product_id' => 'required|integer|exists:products,id',
        ]);

        $customer = $this->customer();
        $result = $customer->wishlist()->toggle($data['product_id']);
        $inWishlist = ! empty($result['attached']);

        return response()->json([
            'in_wishlist' => $inWishlist,
            'count'       => $customer->wishlist()->count(),
            'message'     => $inWishlist ? 'Agregado a tus favoritos ♥' : 'Quitado de tus favoritos',
        ]);
    }

    public function destroy(Product $product): JsonResponse
    {
        $customer = $this->customer();
        $customer->wishlist()->detach($product->id);

        return response()->json([
            'in_wishlist' => false,
            'count'       => $customer->wishlist()->count(),
            'message'     => 'Quitado de tus favoritos',
        ]);
    }
}
