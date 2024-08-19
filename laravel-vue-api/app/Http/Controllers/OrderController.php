<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Cart;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function create(Request $request, $userId)
    {
        $cart = Cart::where('user_id', $userId)->with('items.product')->firstOrFail();
        $order = Order::create([
            'user_id' => $userId,
            'total' => $cart->items->sum(fn($item) => $item->product->price * $item->quantity),
            'status' => 'pending',
        ]);

        foreach ($cart->items as $cartItem) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $cartItem->product_id,
                'quantity' => $cartItem->quantity,
                'price' => $cartItem->product->price,
            ]);
        }

        $cart->delete();

        return response()->json($order, 201);
    }

    public function show($id)
    {
        $order = Order::with('items.product')->findOrFail($id);
        return response()->json($order);
    }
    public function showOrderSummary($id)
    {
        $order = Order::with('items.product')->findOrFail($id);
        return view('order.show', compact('order'));
    }
}
