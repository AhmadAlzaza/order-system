<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Http\Controllers\OrderItemController;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $orders = $user->orders;
        return response()->json($orders);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOrderRequest $request)
    {
        $order = DB::transaction(function () use ($request) {
            $order = Order::create([
                'user_id'     => $request->user()->id,
                'status'      => 'pending',
                'total_price' => 0
            ]);

            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['product_id']);
                if ($item['quantity'] <= $product->stock) {
                    OrderItem::create([
                        'order_id'   => $order->id,
                        'product_id' => $item['product_id'],
                        'quantity'   => $item['quantity'],
                        'price'      => $product->price
                    ]);

                    $order->total_price += $item['quantity'] * $product->price;
                    $product->stock -= $item['quantity'];
                    $product->save();
                } else {
                    throw new \Exception("Insufficient stock for product: " . $product->name);
                }
            }

            $order->save();
            return $order;
        });

        return response()->json($order);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id, Request $request)
    {
        $order = Order::findOrFail($id);
        $user = $request->user();
        if ($order->user_id == $user->id) {
            return response()->json($order);
        } else {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOrderRequest $request, string $id)
    {
        $order = Order::findOrFail($id);
        if ($order->status != 'pending') {
            return response()->json(['message' => 'cant be able to change']);
        } else {

            $user = $request->user();
            if ($order->user_id == $user->id) {
                $order->update(['status' => $request->status]);
                return response()->json($order);
            } else {
                return response()->json(['message' => 'Unauthorized'], 403);
            }
        }
    }
    public function cancel(Request $request, string $id)
    {
        $order = Order::findOrFail($id);
        if ($order->status != 'pending') {
            return response()->json(['message' => 'cant be able to change']);
        } else {

            $user = $request->user();
            if ($order->user_id == $user->id) {
                $order->update(['status' => 'cancelled']);
                return response()->json($order);
            } else {
                return response()->json(['message' => 'Unauthorized'], 403);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        $order = Order::findOrFail($id);
        if ($order->status != 'pending') {
            return response()->json(['message' => 'cant be able to change']);
        } else {
            $user = $request->user();
            if ($order->user_id == $user->id) {
                $order->delete();
                return response()->json('order Deleted');
            } else {
                return response()->json(['message' => 'Unauthorized'], 403);
            }
        }
    }
}
