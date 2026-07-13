<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = Order::where('client_id', $request->user()->id)
            ->with('items.meal')
            ->latest()
            ->paginate(15);

        return view('client.orders.index', compact('orders'));
    }

    public function show(Request $request, Order $order)
    {
        abort_unless($order->client_id === $request->user()->id, 403);

        $order->load(['items.meal', 'agent']);

        return view('client.orders.show', compact('order'));
    }
}
