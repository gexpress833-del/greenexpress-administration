<?php

namespace App\Http\Controllers\Cuisinier;

use App\Http\Controllers\Controller;
use App\Models\Order;
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

        $order->update(['status' => $validated['status']]);

        $statusLabels = [
            'confirmed' => 'confirmée',
            'preparing' => 'en préparation',
            'delivering' => 'prête pour livraison',
        ];

        return redirect()->route('cuisinier.orders.index')
            ->with('success', 'Commande marquée comme ' . ($statusLabels[$validated['status']] ?? $validated['status']) . '.');
    }

    public function print(Order $order)
    {
        $order->load(['items.meal', 'agent', 'client']);

        $totalLabel = $order->currency === 'fc'
            ? number_format($order->total_amount_fc, 0, ',', '.') . ' FC'
            : '$ ' . number_format($order->total_amount, 2);
        $qrData = "GREEN EXPRESS\nBon de préparation\nCommande: {$order->code}\nClient: " . ($order->client_name ?? 'N/A') . "\nTotal: {$totalLabel}\nVérifier sur: green-express.cd";

        $qrOptions = new \chillerlan\QRCode\QROptions();
        $qrOptions->outputInterface = \chillerlan\QRCode\Output\QRGdImagePNG::class;
        $qrOptions->scale = 5;

        $qrCode = new \chillerlan\QRCode\QRCode($qrOptions);
        $qrCodePng = $qrCode->render($qrData);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('cuisinier.orders.print', compact('order', 'qrCodePng'));

        return $pdf->stream("bon-preparation-{$order->code}.pdf");
    }
}
