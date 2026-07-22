<?php

namespace App\Http\Controllers\Livreur;

use App\Events\OrderValidatedByClient;
use App\Http\Controllers\Controller;
use App\Models\Delivery;
use App\Models\DeliveryPoint;
use App\Models\Order;
use App\Models\User;
use App\Notifications\DeliveryAssigned;
use App\Notifications\DeliveryTaken;
use App\Notifications\DeliveryValidated;
use App\Services\ActivityLogService;
use App\Services\DeliveryService;
use App\Services\NotificationService;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DeliveryController extends Controller
{
    public function index(Request $request)
    {
        $userId = $request->user()->id;

        $deliveries = Delivery::where(function ($q) use ($userId) {
            $q->where('livreur_id', $userId)
                ->orWhereNull('livreur_id');
        })
            ->whereHas('order', function ($oq) {
                $oq->whereIn('status', ['confirmed', 'preparing', 'delivering', 'delivered'])
                    ->whereNotNull('admin_validated_at');
            })
            ->with(['order.items.meal', 'order.client', 'livreur'])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = '%'.$request->search.'%';
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
        $delivery->load(['order.items.meal', 'order.agent']);

        if (! in_array($delivery->order->status, ['confirmed', 'preparing', 'delivering', 'delivered']) || $delivery->order->admin_validated_at === null) {
            return redirect()->route('livreur.deliveries.index')
                ->with('error', 'Cette commande n\'est pas encore validée par l\'administrateur. Veuillez patienter avant de scanner.');
        }

        $isAssignedToMe = $delivery->livreur_id === $request->user()->id;
        $isUnassigned = $delivery->livreur_id === null;

        if (! $isAssignedToMe && ! $isUnassigned) {
            return redirect()->route('livreur.deliveries.index')
                ->with('error', 'Cette livraison est assignée à un autre livreur.');
        }

        return view('livreur.deliveries.show', compact('delivery', 'isAssignedToMe', 'isUnassigned'));
    }

    public function assign(Request $request, Delivery $delivery, DeliveryService $deliveryService)
    {
        abort_unless($delivery->livreur_id === null, 403, 'Cette livraison est déjà assignée.');

        // Ne pas assigner une commande non validée par l'admin
        if (! in_array($delivery->order->status, ['confirmed', 'preparing']) || $delivery->order->admin_validated_at === null) {
            return redirect()->route('livreur.deliveries.index')
                ->with('error', 'Cette commande n\'est pas encore validée par l\'administrateur. Vous ne pouvez pas la prendre en charge.');
        }

        try {
            $deliveryService->assign($delivery, $request->user()->id);
        } catch (\DomainException $e) {
            return redirect()->route('livreur.deliveries.index')
                ->with('error', $e->getMessage());
        }

        $notificationService = app(NotificationService::class);
        if ($delivery->order->subscription_id) {
            $notificationService->livreurSubscriptionDeliveryAssigned($request->user(), $delivery);
        } else {
            $notificationService->livreurDeliveryAssigned($request->user(), $delivery);
        }

        // Notifier l'agent et les admins que la livraison a été prise en charge
        if ($delivery->order->agent) {
            $delivery->order->agent->notify(new DeliveryAssigned($delivery));
        }
        User::where('role', 'admin')->get()->each(fn ($admin) => $admin->notify(new DeliveryTaken($delivery)));

        // Notifier le client qu'un livreur a pris en charge sa commande
        if ($delivery->order->client_id) {
            $client = User::find($delivery->order->client_id);
            if ($client) {
                $notificationService->clientDeliveryAssigned($client, $delivery);
            }
        }

        return redirect()->route('livreur.deliveries.index')
            ->with('success', 'Livraison prise en charge.');
    }

    public function deliver(Request $request, Delivery $delivery, DeliveryService $deliveryService)
    {
        abort_unless($delivery->livreur_id === $request->user()->id, 403);

        $order = $delivery->order;

        $check = $deliveryService->canDeliver($delivery);
        if (! $check['allowed']) {
            return redirect()->route('livreur.deliveries.show', $delivery)
                ->with('error', $check['message']);
        }

        $deliveryService->deliver($delivery);

        app(ActivityLogService::class)->logFromRequest($request, 'delivery_delivered', Delivery::class, $delivery->id, 'Livreur marked delivery as delivered for order '.$order->code);

        app(NotificationService::class)->livreurDeliveryPending($request->user(), $delivery);

        return redirect()->route('livreur.deliveries.index')
            ->with('success', 'Livraison marquée comme livrée. En attente de validation du client.');
    }

    public function qrScanForm(Request $request)
    {
        $orderId = $request->query('order_id');
        $code = $request->query('code');

        if (! $orderId || ! $code) {
            return redirect()->route('livreur.deliveries.index')
                ->with('error', 'QR code invalide ou incomplet.');
        }

        $order = Order::find($orderId);
        if (! $order) {
            return redirect()->route('livreur.deliveries.index')
                ->with('error', 'Commande introuvable.');
        }

        $delivery = Delivery::where('order_id', $order->id)->first();
        if (! $delivery) {
            return redirect()->route('livreur.deliveries.index')
                ->with('error', 'Aucune livraison trouvée pour cette commande.');
        }

        return view('livreur.deliveries.qr-confirm', compact('order', 'delivery', 'code'));
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
            return redirect()->route('livreur.deliveries.index')
                ->with('error', 'Aucune livraison trouvée pour cette commande. Vérifiez le code scanné.');
        }

        // Vérifier que la commande est validée par admin
        if (! in_array($order->status, ['confirmed', 'preparing', 'delivering', 'delivered']) || $order->admin_validated_at === null) {
            return redirect()->route('livreur.deliveries.index')
                ->with('error', 'Cette commande n\'est pas encore validée par l\'administrateur. Le QR ne peut pas être scanné pour le moment.');
        }

        // Vérifier le code AVANT toute mutation
        if (strtoupper($request->code) !== $order->client_validation_code) {
            return redirect()->route('livreur.deliveries.index')
                ->with('error', 'Code QR invalide.');
        }

        // Vérifier que la livraison n'est pas assignée à un autre livreur
        if ($delivery->livreur_id !== null && $delivery->livreur_id !== $request->user()->id) {
            return redirect()->route('livreur.deliveries.index')
                ->with('error', 'Cette livraison est déjà assignée à un autre livreur. Vous ne pouvez pas la scanner.');
        }

        $check = $deliveryService->canDeliver($delivery);
        if (! $check['allowed']) {
            return redirect()->route('livreur.deliveries.show', $delivery)
                ->with('error', $check['message']);
        }

        $alreadyValidated = $order->client_validated_at !== null;

        // Auto-assigner et valider dans une transaction verrouillée
        $result = $deliveryService->validateByQr($delivery, $request->user()->id, $request->code);

        if (! $result['success']) {
            return redirect()->route('livreur.deliveries.show', $delivery)
                ->with('error', $result['message']);
        }

        $order->refresh();
        $delivery->refresh();

        if (! $alreadyValidated) {
            try {
                OrderValidatedByClient::dispatch($order);

                if ($delivery->livreur_id) {
                    $this->creditDeliveryPoints($delivery, 15, 'Points gagnés pour livraison validée par le client (QR)');
                    app(NotificationService::class)->livreurDeliveryValidated($request->user(), $delivery, (bool) $order->subscription_id);
                }

                $this->notifyDeliveryValidated($order, 'QR scan');

                if ($order->client_id) {
                    $client = User::find($order->client_id);
                    if ($client) {
                        app(NotificationService::class)->clientOrderDelivered($client, $order);
                    }
                }

                app(ActivityLogService::class)->logFromRequest($request, 'delivery_validated_by_qr', Order::class, $order->id, 'Livreur validated delivery by QR scan for order '.$order->code);
            } catch (\Throwable $exception) {
                Log::error('Post-validation processing failed after QR validation.', [
                    'delivery_id' => $delivery->id,
                    'order_id' => $order->id,
                    'exception' => $exception,
                ]);
            }
        }

        return redirect()->route('livreur.deliveries.show', $delivery)
            ->with('reward', 'Livraison validée par QR ! Vous recevez 15 points.')
            ->with('validation_code', $request->code);
    }

    public function validateByCode(Request $request, Delivery $delivery, DeliveryService $deliveryService)
    {
        if ($delivery->livreur_id !== $request->user()->id) {
            return redirect()->route('livreur.deliveries.index')
                ->with('error', 'Cette livraison ne vous est pas assignée.');
        }

        $request->validate([
            'validation_code' => ['required', 'string', 'size:6'],
        ]);

        $order = $delivery->order;

        // Vérifier que la commande est validée par admin
        if (! in_array($order->status, ['confirmed', 'preparing', 'delivering', 'delivered']) || $order->admin_validated_at === null) {
            return redirect()->route('livreur.deliveries.show', $delivery)
                ->with('error', 'Cette commande n\'est pas encore validée par l\'administrateur. Le code ne peut pas être validé pour le moment.');
        }

        $check = $deliveryService->canDeliver($delivery);
        if (! $check['allowed']) {
            return redirect()->route('livreur.deliveries.show', $delivery)
                ->with('error', $check['message']);
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

        $order->refresh();

        try {
            OrderValidatedByClient::dispatch($order);

            if ($delivery->livreur_id) {
                $this->creditDeliveryPoints($delivery, 15, 'Points gagnés pour livraison validée par le client');
                app(NotificationService::class)->livreurDeliveryValidated($request->user(), $delivery, (bool) $order->subscription_id);
            }

            $this->notifyDeliveryValidated($order, 'code client');

            if ($order->client_id) {
                $client = User::find($order->client_id);
                if ($client) {
                    app(NotificationService::class)->clientOrderDelivered($client, $order);
                }
            }

            app(ActivityLogService::class)->logFromRequest($request, 'delivery_validated_by_client', Order::class, $order->id, 'Livreur validated delivery by client code for order '.$order->code);
        } catch (\Throwable $exception) {
            Log::error('Post-validation processing failed after client code validation.', [
                'delivery_id' => $delivery->id,
                'order_id' => $order->id,
                'exception' => $exception,
            ]);
        }

        return redirect()->route('livreur.deliveries.show', $delivery)
            ->with('reward', 'Livraison validée ! Vous recevez 15 points de livraison.');
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

    private function creditDeliveryPoints(Delivery $delivery, int $points, string $description): void
    {
        if ($delivery->deliveryPoints()->where('points', '>', 0)->exists()) {
            return;
        }

        DeliveryPoint::create([
            'delivery_id' => $delivery->id,
            'livreur_id' => $delivery->livreur_id,
            'points' => $points,
            'description' => $description,
        ]);
    }
}
