<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
{
    // Show cart items based on session or user ID
    public function show(Request $request, $userId = null)
    {
        if ($userId === 'session') {
            $cart = session('cart', []);
            return response()->json($cart);
        }
        if ($userId) {
            $cart = Cart::with('items.product')->where('user_id', $userId)->first();
            return response()->json($cart);
        }

        return response()->json([]);
    }

    // Add item to the cart
    public function addItem(Request $request, $userId = null)
    {
        if ($userId === 'session') {
            $cart = $request->session()->get('cart', []);
            \Log::info('Cart session before adding item:', $cart);

            $productId = $request->product_id;
            $quantity = $request->quantity;

            if (isset($cart[$productId])) {
                $cart[$productId]['quantity'] += $quantity;
            } else {
                $cart[$productId] = [
                    'product_id' => $productId,
                    'quantity' => $quantity,
                ];
            }

            $request->session()->put('cart', $cart);

            \Log::info('Cart session after adding item:', $request->session()->get('cart'));

            return response()->json($cart);
        }

        if ($userId) {
            $cart = Cart::firstOrCreate(['user_id' => $userId]);
            $product = Product::findOrFail($request->product_id);

            $cartItem = $cart->items()->updateOrCreate(
                ['product_id' => $product->id],
                ['quantity' => $request->quantity]
            );

            return response()->json($cartItem, 201);
        }
    }

    // Remove item from the cart
    public function removeItem(Request $request, $userId = null)
    {
        $cartItemId = $request->input('cart_item_id');

        if ($userId) {
            $cartItem = CartItem::where('cart_id', $userId)->findOrFail($cartItemId);
        } else {
            $cart = $this->getSessionCart();
            $cartItem = $cart->items()->findOrFail($cartItemId);
        }

        $cartItem->delete();

        return response()->json(null, 204);
    }

    // Transfer session cart items to the user cart after login
    public function transferSessionCartToUser($userId)
    {
        $sessionCart = session('cart', []);

        if (!empty($sessionCart)) {
            $cart = Cart::firstOrCreate(['user_id' => $userId]);

            foreach ($sessionCart as $productId => $sessionCartItem) {
                $cart->items()->updateOrCreate(
                    ['product_id' => $productId],
                    ['quantity' => $sessionCartItem['quantity']]
                );
            }

            // Clear session cart
            Session::forget('cart');
        }
    }


    // Get session cart
    private function getSessionCart($create = false)
    {
        $sessionCart = Session::get('cart');

        if (!$sessionCart && $create) {
            $sessionCart = new Cart();
            $sessionCart->save();
            Session::put('cart', $sessionCart);
        }

        return $sessionCart;
    }

    // Store session cart
    private function storeSessionCart($cart)
    {
        Session::put('cart', $cart);
    }
}
