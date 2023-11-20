<?php

namespace App\Http\Controllers;

use App\Models\order;
use App\Http\Requests\StoreorderRequest;
use App\Http\Requests\UpdateorderRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Resources\OrderResource;

class OrderController extends Controller
{
   
    public function index()
    {
        //get all orders
        $orders = order::all();
        return OrderResource::collection($orders);
    }

    public function show()
    {
        //get user order
        $orders = order::where('user_id', auth()->user()->id)->get();
        return OrderResource::collection($orders);
    }

    //showing order details
    public function showOrderDetails(order $order)
    {
        //get order details
        $orderItems = $order->orderItems;
        return $orderItems;
    }

    //updating order to change it from processing to completed or delivered
    public function updateOrderStatus(Order $order, $newStatus)
    {
        // Update order status
        $order->status = $newStatus;
        $order->save();

        return new OrderResource($order);
    }

    //filter orders based on date range or order status 
    public function filterOrders(Request $request)
{
    // Get parameters from the request
    $startDate = $request->input('start_date');
    $endDate = $request->input('end_date');
    $status = $request->input('status');

    // Start building the query
    $query = Order::query();

    // Add date range filter if provided
    if ($startDate && $endDate) {
        $query->whereBetween('created_at', [Carbon::parse($startDate), Carbon::parse($endDate)]);
    }

    // Add order status filter if provided
    if ($status) {
        $query->where('status', $status);
    }

    // Execute the query
    $filteredOrders = $query->get();

    return response()->json(['orders' => $filteredOrders]);
}

}
