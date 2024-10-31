<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
class CartController extends Controller
{
    // Show cart items
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
        DB::beginTransaction();

        try {
            $productId = $request->product_id;
            $quantity = $request->quantity;
            $product = Product::findOrFail($productId);

            if ($product->stock < $quantity) {
                return response()->json(['error' => 'Not enough stock available'], 400);
            }

            // Handle session-based cart
            if ($userId === 'session') {
                $cart = $request->session()->get('cart', []);

                if (isset($cart[$productId])) {
                    $cart[$productId]['quantity'] += $quantity;
                } else {
                    $cart[$productId] = [
                        'product_id' => $productId,
                        'quantity' => $quantity,
                        'product_name' => $product->name,  // Store product name
                        'product_price' => $product->price, // Store product price
                    ];
                }

                $request->session()->put('cart', $cart);

                DB::commit();

                return response()->json($cart);
            }

            // Handle user-based cart
            if ($userId) {
                $cart = Cart::firstOrCreate(['user_id' => $userId]);

                $cartItem = $cart->items()->updateOrCreate(
                    ['product_id' => $product->id],
                    [
                        'quantity' => DB::raw("quantity + $quantity"),
                        'product_name' => $product->name, 
                        'product_price' => $product->price 
                    ]
                );

                DB::commit();

                return response()->json($cartItem, 201);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to add item to the cart: ' . $e->getMessage()], 500);
        }
    }

    // Update item in the cart
    public function updateItem(Request $request, $userId = null)
    {
        DB::beginTransaction();

        try {
            $productId = $request->input('product_id');
            $quantity = $request->input('quantity');

            if ($userId === 'session') {
                $cart = $request->session()->get('cart', []);

                if (isset($cart[$productId])) {
                    $cart[$productId]['quantity'] = $quantity;
                    $request->session()->put('cart', $cart);
                } else {
                    return response()->json(['error' => 'Item not found in session cart'], 404);
                }
            } elseif ($userId) {
                $cartItem = CartItem::where('cart_id', $userId)->where('product_id', $productId)->firstOrFail();
                $cartItem->quantity = $quantity;
                $cartItem->save();
            }

            DB::commit();
            return response()->json(null, 204); // Successful update with no content
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to update item in the cart: ' . $e->getMessage()], 500);
        }
    }

    // Remove item from the cart
    public function removeItem(Request $request, $userId = null)
    {
        DB::beginTransaction();
        try {
            // Get product_id from query parameters
            $productId = $request->query('product_id');
            Log::info("ProductId: $productId");

            if ($userId === 'session') {
                // For session cart
                $cart = $request->session()->get('cart', []);
                if (isset($cart[$productId])) {
                    unset($cart[$productId]);
                    $request->session()->put('cart', $cart);
                } else {
                    return response()->json(['error' => 'Item not found in session cart'], 404);
                }
            } elseif ($userId) {
                // For user cart
                $cartItem = CartItem::where('cart_id', $userId)
                    ->where('product_id', $productId)
                    ->firstOrFail();
                $cartItem->delete();
            }

            DB::commit();
            return response()->json(null, 204);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to remove item from cart: ' . $e->getMessage()], 500);
        }
    }


    // Transfer session cart items to the user cart after login
    public function transferSessionCartToUser($userId)
    {
        DB::beginTransaction();

        try {
            $sessionCart = session('cart', []);

            if (!empty($sessionCart)) {
                $cart = Cart::firstOrCreate(['user_id' => $userId]);

                foreach ($sessionCart as $productId => $sessionCartItem) {
                    $cart->items()->updateOrCreate(
                        ['product_id' => $productId],
                        [
                            'quantity' => $sessionCartItem['quantity'],
                            'product_name' => $sessionCartItem['product_name'], // Transfer product name
                            'product_price' => $sessionCartItem['product_price'], // Transfer product price
                        ]
                    );
                }

                // Clear session cart
                Session::forget('cart');
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("Failed to transfer session cart to user: {$e->getMessage()}");
        }
    }

    // Helper methods for session cart
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

    private function storeSessionCart($cart)
    {
        Session::put('cart', $cart);
    }
}
