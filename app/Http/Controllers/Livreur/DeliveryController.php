<?php

namespace App\Http\Controllers\Livreur;

use App\Http\Controllers\Controller;
use App\Models\Delivery;
use App\Models\Order;
use App\Models\User;
use App\Events\OrderValidatedByClient;
use App\Notifications\DeliveryAssigned;
use App\Notifications\DeliveryValidated;
use App\Services\ActivityLogService;
use App\Services\DeliveryService;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;

class DeliveryController extends Controller
{
    public function index(Request $request)
    {
        $userId = $request->user()->id;

        $deliveries = Delivery::where(function ($q) use ($userId) {
                $q->where('livreur_id', $userId)
                  ->orWhereNull('livreur_id');
            })
            // Ne voir que les commandes validées par l'admin (confirmed et plus)
            ->whereHas('order', function ($oq) {
                $oq->whereIn('status', ['confirmed', 'preparing', 'delivering', 'delivered']);
            })
            ->with('order')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = '%' . $request->search . '%';
                $query->where(function ($q) use ($search) {
                    $q->where('delivery_code', 'like', $search)
                        ->orWhereHas('order', function ($oq) use ($search) {
                            $oq->where('code', 'like', $search)
                                ->orWhere('client_name', 'like', $search)
                                ->orWhere('client_phone', 'like', $search);
                        });
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();
        return view('livreur.deliveries.index', compact('deliveries'));
    }

    public function show(Request $request, Delivery $delivery)
    {
        abort_unless($delivery->livreur_id === $request->user()->id || $delivery->livreur_id === null, 403);

        $delivery->load(['order.items.meal', 'order.agent']);

        // Ne pas accéder aux commandes non validées par l'admin
        if (! in_array($delivery->order->status, ['confirmed', 'preparing', 'delivering', 'delivered'])) {
            abort(403, 'Cette commande n\'est pas encore validée par l\'administrateur.');
        }

        return view('livreur.deliveries.show', compact('delivery'));
    }

    public function assign(Request $request, Delivery $delivery, DeliveryService $deliveryService)
    {
        abort_unless($delivery->livreur_id === null, 403, 'Cette livraison est déjà assignée.');

        // Ne pas assigner une commande non validée par l'admin
        if (! in_array($delivery->order->status, ['confirmed', 'preparing'])) {
            abort(403, 'Cette commande n\'est pas encore validée par l\'administrateur.');
        }

        $deliveryService->assign($delivery, $request->user()->id);

        // Notifier l'agent que la livraison a été prise en charge
        if ($delivery->order->agent) {
            $delivery->order->agent->notify(new DeliveryAssigned($delivery));
        }

        return redirect()->route('livreur.deliveries.index')
            ->with('success', 'Livraison prise en charge.');
    }

    public function validateQrForm(Request $request, DeliveryService $deliveryService)
    {
        $request->validate([
            'order_id' => ['required', 'integer', 'exists:orders,id'],
            'code' => ['required', 'string', 'size:6'],
        ]);

        $order = Order::findOrFail($request->order_id);
        $delivery = Delivery::where('order_id', $order->id)->first();

        if (! $delivery) {
            abort(403, 'Aucune livraison trouvée pour cette commande.');
        }

        // Vérifier que la commande est validée par admin
        if (! in_array($order->status, ['confirmed', 'preparing', 'delivering', 'delivered'])) {
            abort(403, 'Cette commande n\'est pas encore validée par l\'administrateur.');
        }

        // Auto-assigner si non assignée
        if ($delivery->livreur_id === null) {
            $delivery->livreur_id = $request->user()->id;
            $delivery->status = 'assigned';
            $delivery->picked_up_at = now();
            $delivery->save();

            $order->status = 'delivering';
            $order->save();
        } elseif ($delivery->livreur_id !== $request->user()->id) {
            abort(403, 'Cette livraison est assignée à un autre livreur.');
        }

        if (strtoupper($request->code) !== $order->client_validation_code) {
            return redirect()->route('livreur.deliveries.show', $delivery)
                ->with('error', 'Code QR invalide.');
        }

        $alreadyValidated = $order->client_validated_at !== null;

        // Validation immédiate au scan QR
        $result = $deliveryService->validateByClient($delivery, $request->code);

        if (! $result['success']) {
            return redirect()->route('livreur.deliveries.show', $delivery)
                ->with('error', $result['message']);
        }

        $order->refresh();

        if (! $alreadyValidated) {
            OrderValidatedByClient::dispatch($order);

            // Notifier admin et agent
            $this->notifyDeliveryValidated($order, 'QR scan');

            app(ActivityLogService::class)->logFromRequest($request, 'delivery_validated_by_qr', Order::class, $order->id, 'Livreur validated delivery by QR scan for order ' . $order->code);
        }

        if ($order->agent?->phone) {
            $whatsappLink = app(WhatsAppService::class)->commissionCreditedLink(
                $order->agent->phone,
                $order->code,
                0.00,
                'daily_commission'
            );
            return redirect()->route('livreur.deliveries.show', $delivery)
                ->with('success', 'Livraison validée par QR. Points et badges crédités pour l\'agent. Commission calculée ce soir.')
                ->with('whatsapp_link', $whatsappLink);
        }

        return redirect()->route('livreur.deliveries.show', $delivery)
            ->with('success', 'Livraison validée par QR. Points et badges crédités pour l\'agent. Commission calculée ce soir.');
    }

    public function validateByCode(Request $request, Delivery $delivery, DeliveryService $deliveryService)
    {
        abort_unless($delivery->livreur_id === $request->user()->id, 403);

        $request->validate([
            'validation_code' => ['required', 'string', 'size:6'],
        ]);

        $order = $delivery->order;

        // Vérifier que la commande est validée par admin
        if (! in_array($order->status, ['confirmed', 'preparing', 'delivering', 'delivered'])) {
            abort(403, 'Cette commande n\'est pas encore validée par l\'administrateur.');
        }

        $alreadyValidated = $order->client_validated_at !== null;

        $result = $deliveryService->validateByClient($delivery, $request->validation_code);

        if (! $result['success']) {
            return redirect()->route('livreur.deliveries.show', $delivery)
                ->with('error', $result['message']);
        }

        if ($alreadyValidated) {
            return redirect()->route('livreur.deliveries.show', $delivery)
                ->with('success', $result['message']);
        }

        // Dispatcher l'event pour créditer points, badges, etc.
        OrderValidatedByClient::dispatch($order);

        // Notifier admin et agent
        $this->notifyDeliveryValidated($order, 'code client');

        app(ActivityLogService::class)->logFromRequest($request, 'delivery_validated_by_client', Order::class, $order->id, 'Livreur validated delivery by client code for order ' . $order->code);

        if ($order->agent?->phone) {
            $whatsappLink = app(WhatsAppService::class)->commissionCreditedLink(
                $order->agent->phone,
                $order->code,
                0.00,
                'daily_commission'
            );
            return redirect()->route('livreur.deliveries.show', $delivery)
                ->with('success', 'Livraison validée par le client. Points et badges crédités pour l\'agent. Commission calculée ce soir.')
                ->with('whatsapp_link', $whatsappLink);
        }

        return redirect()->route('livreur.deliveries.show', $delivery)
            ->with('success', 'Livraison validée par le client. Points et badges crédités pour l\'agent. Commission calculée ce soir.');
    }

    public function notifyClient(Request $request, Delivery $delivery)
    {
        abort_unless($delivery->livreur_id === $request->user()->id, 403);

        $order = $delivery->order;
        $whatsappLink = app(WhatsAppService::class)->deliveryOnTheWayLink(
            $order->client_phone,
            $order->client_name,
            $order->code
        );

        return redirect()->route('livreur.deliveries.show', $delivery)
            ->with('success', 'Message préparé pour le client.')
            ->with('whatsapp_link', $whatsappLink);
    }

    private function notifyDeliveryValidated(Order $order, string $method): void
    {
        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            $admin->notify(new DeliveryValidated($order, $method));
        }

        if ($order->agent) {
            $order->agent->notify(new DeliveryValidated($order, $method));
        }
    }
}
