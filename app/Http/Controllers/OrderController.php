<?php

namespace App\Http\Controllers;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\Product;
class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $User = $request->user();
        $orders = $User->orders;
        return response()->json($orders);

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $User = $request->user();
        $order = Order::create(['user_id' => $User->id,'status' => $request->status,'total_price' => 0]);
        return response()->json($order);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id,Request $request)
    {
        $order = Order::findOrFail($id);
        $user = $request->user();
        if ($order->user_id == $user->id)
        {
            return response()->json($order);
        }
        else
        {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $order = Order::findOrFail($id);
        if($order->status != 'pending'){
            return response()->json(['message'=>'cant be able to change']);
        }
        else {

            $user = $request->user();
            if ($order->user_id == $user->id)
            {
                  $order->update(['status' => $request->status]);
                return response()->json($order);
            }
            else
            {
                return response()->json(['message' => 'Unauthorized'], 403);
            }
        }


    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request,string $id)
    {
        $order = Order::findOrFail($id);
        if($order->status != 'pending'){
            return response()->json(['message'=>'cant be able to change']);
        }
        else{
            $user = $request->user();
        if ($order->user_id == $user->id)
        {
              $order->delete();
              return response()->json('order Deleted');
        }
        else
        {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        }

    }
}
