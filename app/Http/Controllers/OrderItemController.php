<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Order;
use App\Http\Resources\OrderItemResource;
use App\Http\Requests\StoreOrderItemRequest;
use App\Http\Requests\UpdateOrderItemRequest;

class OrderItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return OrderItemResource::collection(
            $request->user()->orderItems()->with('product')->paginate(15)
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOrderItemRequest $request)
    {
        $product = Product::findOrFail($request->product_id);
        $order = Order::findOrFail($request->order_id);
        if ($order->user_id != $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        $orderItem = OrderItem::create(
            [
                'order_id' => $request->order_id,
                'product_id' => $request->product_id,
                'quantity' => $request->quantity,
                'price' => $product->price
            ]
        );

        $totalPrice = $order->total_price + ($request->quantity * $product->price);
        $order->update(['total_price' => $totalPrice]);
        return new OrderItemResource($orderItem);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $orderItem = OrderItem::findOrFail($id);

        return new OrderItemResource($orderItem->load('product'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOrderItemRequest $request, string $id)
    {
        $product = Product::findOrFail($request->product_id);
        $orderItem = OrderItem::findOrFail($id);

        $oldQuantity = $orderItem->quantity;
        $oldPrice    = $orderItem->price;
        $orderItem->update(
            [
                'order_id' => $request->order_id,
                'product_id' => $request->product_id,
                'quantity' => $request->quantity,
                'price' => $product->price
            ]
        );

        $order = Order::findOrFail($request->order_id);
        $totalPrice = $order->total_price - ($oldQuantity * $oldPrice) + ($request->quantity * $product->price);
        $order->update(['total_price' => $totalPrice]);
        return new OrderItemResource($orderItem->load('product'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $orderItem = OrderItem::findOrFail($id);
        $order = Order::findOrFail($orderItem->order_id);
        $total = $orderItem->quantity * $orderItem->price;
        $orderItem->delete();
        $order->total_price -= $total;
        $order->save();
        return response()->json(['message' => 'Order item deleted']);
    }
}
