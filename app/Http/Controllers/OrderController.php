<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = Order::where('user_id', $request->user()->id)
                    ->orderBy('created_at', 'desc')
                    ->get();
        return view('orders.index', compact('orders'));
    }
}
