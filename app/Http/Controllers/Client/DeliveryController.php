<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Delivery;
use Illuminate\Http\Request;

class DeliveryController extends Controller
{
    public function index(Request $request)
    {
        $deliveries = Delivery::whereHas('order', function ($q) use ($request) {
            $q->where('client_id', $request->user()->id);
        })->with('order', 'livreur')->latest()->paginate(15);

        return view('client.deliveries.index', compact('deliveries'));
    }

    public function show(Request $request, Delivery $delivery)
    {
        abort_unless($delivery->order->client_id === $request->user()->id, 403);

        $delivery->load(['order.items.meal', 'livreur']);

        return view('client.deliveries.show', compact('delivery'));
    }
}
