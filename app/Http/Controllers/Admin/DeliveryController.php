<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Delivery;
use App\Models\DeliveryPoint;
use App\Models\Order;
use App\Models\User;
use App\Notifications\DeliveryAssigned;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DeliveryController extends Controller
{
    public function index(Request $request)
    {
        $deliveries = Delivery::with(['order', 'livreur'])
            ->when($request->filled('search'), function ($q) use ($request) {
                $term = '%'.$request->search.'%';
                $q->where('delivery_code', 'like', $term)
                    ->orWhereHas('order', fn ($o) => $o->where('code', 'like', $term))
                    ->orWhereHas('livreur', fn ($l) => $l->where('name', 'like', $term));
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.deliveries.index', compact('deliveries'));
    }

    public function create()
    {
        $orders = Order::whereIn('status', ['confirmed', 'preparing'])->get();
        $livreurs = User::where('role', 'livreur')->where('is_active', true)->get();

        return view('admin.deliveries.create', compact('orders', 'livreurs'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'order_id' => ['required', 'exists:orders,id'],
            'livreur_id' => ['required', 'exists:users,id'],
        ]);

        $order = Order::findOrFail($data['order_id']);

        if ($order->admin_validated_at === null) {
            return redirect()->route('admin.deliveries.create')
                ->with('error', 'Cette commande n\'est pas encore validée par l\'administrateur.');
        }

        if ($order->delivery()->exists()) {
            return redirect()->route('admin.deliveries.create')
                ->with('error', 'Une livraison existe déjà pour cette commande.');
        }

        $data['delivery_code'] = 'DLV-'.strtoupper(uniqid());
        $data['status'] = 'assigned';
        $data['picked_up_at'] = now();

        $delivery = Delivery::create($data);
        $order->transitionTo('delivering');

        $delivery->load(['order.agent', 'order.client', 'livreur']);
        $notificationService = app(NotificationService::class);

        try {
            if ($delivery->livreur) {
                if ($order->subscription_id) {
                    $notificationService->livreurSubscriptionDeliveryAssigned($delivery->livreur, $delivery);
                } else {
                    $notificationService->livreurDeliveryAssigned($delivery->livreur, $delivery);
                }
            }

            if ($order->agent) {
                $order->agent->notify(new DeliveryAssigned($delivery));
            }

            if ($order->client_id && $order->client) {
                $notificationService->clientDeliveryAssigned($order->client, $delivery);
            }
        } catch (\Throwable $e) {
            Log::warning('Notification dispatch failed during admin delivery assignment.', [
                'delivery_id' => $delivery->id,
                'error' => $e->getMessage(),
            ]);
        }

        return redirect()->route('admin.deliveries.index')->with('success', 'Livraison assignée.');
    }

    public function penalize(Request $request, Delivery $delivery)
    {
        $data = $request->validate([
            'points' => ['required', 'integer', 'min:1'],
            'description' => ['required', 'string', 'max:255'],
        ]);

        if (! $delivery->livreur_id) {
            return redirect()->route('admin.deliveries.index')
                ->with('error', 'Cette livraison n\'est pas assignée à un livreur.');
        }

        $total = $delivery->livreur->deliveryPoints()->sum('points');
        $requested = (int) $data['points'];

        if ($total - $requested < 0) {
            return redirect()->route('admin.deliveries.index')
                ->with('error', 'Solde insuffisant. Le livreur possède seulement '.$total.' point(s).');
        }

        DeliveryPoint::create([
            'delivery_id' => $delivery->id,
            'livreur_id' => $delivery->livreur_id,
            'points' => -$requested,
            'description' => $data['description'],
        ]);

        return redirect()->route('admin.deliveries.index')
            ->with('success', $requested.' point(s) retiré(s) au livreur.');
    }
}
