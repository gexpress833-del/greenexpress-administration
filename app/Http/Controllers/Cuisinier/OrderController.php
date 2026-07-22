<?php

namespace App\Http\Controllers\Cuisinier;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Services\NotificationService;
use Barryvdh\DomPDF\Facade\Pdf;
use chillerlan\QRCode\Output\QRGdImagePNG;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['items.meal', 'agent'])
            ->whereIn('status', ['confirmed', 'preparing', 'delivering']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $orders = $query->latest()->paginate(20)->withQueryString();

        return view('cuisinier.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load(['items.meal', 'agent']);

        return view('cuisinier.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:confirmed,preparing,delivering'],
        ]);

        $oldStatus = $order->status;
        $order->transitionTo($validated['status']);

        $notificationService = app(NotificationService::class);

        // Notifier les admins quand la préparation commence
        if ($validated['status'] === 'preparing' && $oldStatus !== 'preparing') {
            User::where('role', 'admin')->get()->each(function ($admin) use ($notificationService, $order) {
                $notificationService->cuisinierPreparationStarted($admin, $order);
            });
        }

        // Notifier les livreurs + admins quand la commande est prête
        if ($validated['status'] === 'delivering' && $oldStatus !== 'delivering') {
            User::where('role', 'admin')->get()->each(function ($admin) use ($notificationService, $order) {
                $notificationService->cuisinierOrderReady($admin, $order);
            });
            User::where('role', 'livreur')->get()->each(function ($livreur) use ($notificationService, $order) {
                $notificationService->cuisinierOrderReady($livreur, $order);
            });
        }

        $statusLabels = [
            'confirmed' => 'confirmée',
            'preparing' => 'en préparation',
            'delivering' => 'prête pour livraison',
        ];

        $toastType = $validated['status'] === 'delivering' ? 'success' : 'info';

        return redirect()->route('cuisinier.orders.index')
            ->with($toastType, 'Commande marquée comme '.($statusLabels[$validated['status']] ?? $validated['status']).'.');
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
