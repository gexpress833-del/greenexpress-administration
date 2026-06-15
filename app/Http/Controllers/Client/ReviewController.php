<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\DeliveryReview;
use App\Models\Order;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request, Order $order)
    {
        abort_unless($order->client_id === $request->user()->id, 403);
        abort_unless($order->status === 'delivered', 403);
        abort_if($order->review()->exists(), 403, 'Vous avez déjà évalué cette livraison.');

        $data = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:500'],
        ]);

        DeliveryReview::create([
            'order_id' => $order->id,
            'client_id' => $request->user()->id,
            'livreur_id' => $order->delivery?->livreur_id,
            'rating' => $data['rating'],
            'comment' => $data['comment'],
        ]);

        return redirect()->route('client.orders.show', $order)
            ->with('success', 'Merci pour votre évaluation !');
    }
}
