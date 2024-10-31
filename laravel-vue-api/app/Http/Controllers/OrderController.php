<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    // Create a new order
    public function create(Request $request, $userId)
    {
        DB::beginTransaction();

        try {
            $cart = Cart::where('user_id', $userId)->with('items.product')->firstOrFail();

            // Check if stock is available for each item
            foreach ($cart->items as $cartItem) {
                if ($cartItem->product->stock < $cartItem->quantity) {
                    return response()->json(['error' => "Not enough stock for product {$cartItem->product->name}"], 400);
                }
            }

            // Create order
            $order = Order::create([
                'user_id' => $userId,
                'total' => $cart->items->sum(fn($item) => $item->product->price * $item->quantity),
                'status' => 'pending',
            ]);

            // Create order items and update stock
            foreach ($cart->items as $cartItem) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cartItem->product_id,
                    'quantity' => $cartItem->quantity,
                    'price' => $cartItem->product->price,
                ]);

                // Update product stock
                $cartItem->product->decrement('stock', $cartItem->quantity);
            }

            // Clear cart after order is placed
            $cart->delete();

            DB::commit();

            return response()->json($order, 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Order creation failed: ' . $e->getMessage()], 500);
        }
    }

    // Show order details
    public function show($id)
    {
        $order = Order::with('items.product')->findOrFail($id);
        return response()->json($order);
    }

    // Show order summary
    public function showOrderSummary($id)
    {
        $order = Order::with('items.product')->findOrFail($id);
        return view('order.show', compact('order'));
    }
}
