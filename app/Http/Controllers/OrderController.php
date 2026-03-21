<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Http\Resources\OrderResource;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return OrderResource::collection(
            $request->user()->orders()->with('user')->paginate(15)
        );
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
                $product = Product::lockForUpdate()->findOrFail($item['product_id']);
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
                    abort(422, 'Insufficient stock for product: ' . $product->name);
                }
            }

            $order->save();
            return $order;
        });
        return new OrderResource($order->load('user'));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id, Request $request)
    {
        $order = Order::findOrFail($id);
        $user = $request->user();
        if ($order->user_id == $user->id) {
            return new OrderResource($order->load('user'));
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
            return response()->json(['message' => 'Order cannot be modified in its current status'], 422);
        } else {

            $user = $request->user();
            if ($order->user_id == $user->id) {
                $order->update(['status' => $request->status]);
                return new OrderResource($order->load('user'));
            } else {
                return response()->json(['message' => 'Unauthorized'], 403);
            }
        }
    }
    public function cancel(Request $request, string $id)
    {
        $order = Order::findOrFail($id);
        if ($order->status != 'pending') {
            return response()->json(['message' => 'Order cannot be modified in its current status'], 422);
        } else {

            $user = $request->user();
            if ($order->user_id == $user->id) {
                $order->update(['status' => 'cancelled']);
                return new OrderResource($order->load('user'));
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
            return response()->json(['message' => 'Order cannot be modified in its current status'], 422);
        } else {
            $user = $request->user();
            if ($order->user_id == $user->id) {
                $order->delete();
                return response()->json(['message' => 'order Deleted']);
            } else {
                return response()->json(['message' => 'Unauthorized'], 403);
            }
        }
    }
}
