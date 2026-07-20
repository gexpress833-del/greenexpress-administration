<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Notifications\NewOrderForCuisinier;
use App\Notifications\NewOrderForDelivery;
use App\Notifications\OrderStatusUpdated;
use App\Services\NotificationService;
use Barryvdh\DomPDF\Facade\Pdf;
use chillerlan\QRCode\Output\QRGdImagePNG;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = Order::with(['agent', 'client', 'items.meal'])
            ->when($request->filled('search'), function ($q) use ($request) {
                $term = '%'.$request->search.'%';
                $q->where('code', 'like', $term)
                    ->orWhere('client_name', 'like', $term)
                    ->orWhereHas('agent', fn ($a) => $a->where('name', 'like', $term));
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load(['agent', 'client', 'items.meal', 'delivery.livreur']);

        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate(['status' => 'required|in:pending,confirmed,preparing,delivering,delivered,cancelled']);
        $oldStatus = $order->status;
        $order->status = $request->status;
        if ($request->status === 'delivered') {
            $order->delivered_at = now();
        }
        if ($request->status === 'confirmed' && $oldStatus !== 'confirmed') {
            $order->admin_validated_at = now();
        }
        $order->save();

        $notificationService = app(NotificationService::class);

        try {
            // Notifier l'agent du changement de statut (database + FCM)
            if ($order->agent && $oldStatus !== $request->status) {
                $order->agent->notify(new OrderStatusUpdated($order, $oldStatus));

                $notificationService->notify(
                    $order->agent,
                    'order',
                    'Commande '.($request->status === 'confirmed' ? 'validée' : 'mise à jour'),
                    "Votre commande {$order->code} est maintenant « {$request->status} ».",
                    'order_status_updated',
                    route('agent.orders.show', $order),
                );
            }

            // Notifier tous les livreurs quand la commande est validée par l'admin
            if ($request->status === 'confirmed' && $oldStatus !== 'confirmed') {
                $livreurs = User::where('role', 'livreur')->get();
                foreach ($livreurs as $livreur) {
                    $livreur->notify(new NewOrderForDelivery($order));

                    $notificationService->notify(
                        $livreur,
                        'delivery',
                        'Nouvelle livraison disponible',
                        "La commande {$order->code} a été validée. Vous pouvez la prendre en charge.",
                        'new_order_for_delivery',
                        route('livreur.deliveries.index'),
                    );
                }
            }

            // Notifier tous les cuisiniers quand la commande est validée par l'admin
            if ($request->status === 'confirmed' && $oldStatus !== 'confirmed') {
                $cuisiniers = User::where('role', 'cuisinier')->get();
                foreach ($cuisiniers as $cuisinier) {
                    $cuisinier->notify(new NewOrderForCuisinier($order));
                    $notificationService->cuisinierNewOrder($cuisinier, $order);
                }
            }
        } catch (\Throwable $e) {
            Log::warning('Notification dispatch failed during order status update.', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
        }

        $successMessage = match ($request->status) {
            'confirmed' => 'Commande validée avec succès. Les livreurs et cuisiniers ont été notifiés.',
            'cancelled' => 'Commande annulée.',
            'delivered' => 'Commande marquée comme livrée.',
            default => 'Statut mis à jour.',
        };

        return redirect()->route('admin.orders.show', $order)->with('success', $successMessage);
    }

    public function print(Order $order)
    {
        $order->load(['items.meal', 'agent', 'client']);

        $qrData = "GREEN EXPRESS\nBon de préparation\nCommande: {$order->code}\nClient: ".($order->client_name ?? 'N/A')."\nTotal: $ ".number_format($order->total_amount, 2)."\nVérifier sur: green-express.cd";

        $qrOptions = new QROptions;
        $qrOptions->outputInterface = QRGdImagePNG::class;
        $qrOptions->scale = 5;

        $qrCode = new QRCode($qrOptions);
        $qrCodePng = $qrCode->render($qrData);

        $pdf = Pdf::loadView('cuisinier.orders.print', compact('order', 'qrCodePng'));

        return $pdf->stream("bon-preparation-{$order->code}.pdf");
    }
}
