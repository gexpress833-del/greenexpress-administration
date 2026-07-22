<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Meal;
use App\Models\Order;
use App\Models\User;
use App\Notifications\OrderCreatedClientMail;
use App\Services\ActivityLogService;
use App\Services\NotificationService;
use App\Services\OrderService;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = Order::where('agent_id', $request->user()->id)
            ->with('items.meal')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = '%'.$request->search.'%';
                $query->where(function ($q) use ($search) {
                    $q->where('code', 'like', $search)
                        ->orWhere('client_name', 'like', $search)
                        ->orWhere('client_phone', 'like', $search);
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('agent.orders.index', compact('orders'));
    }

    public function create()
    {
        $meals = Meal::where('status', 'available')->where('is_active', true)->get();

        return view('agent.orders.create', compact('meals'));
    }

    public function store(Request $request, OrderService $orderService)
    {
        $data = $request->validate([
            'client_name' => ['required', 'string', 'max:255'],
            'client_phone' => ['required', 'string', 'max:50'],
            'delivery_address' => ['required', 'string'],
            'delivery_date' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
            'currency' => ['required', 'in:usd,fc'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.meal_id' => ['required', 'exists:meals,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ]);

        $order = $orderService->createOrder($data, $request->user()->id);

        $whatsappLink = app(WhatsAppService::class)->orderConfirmationLink(
            $data['client_phone'],
            $data['client_name'],
            $order->code,
            (float) $order->total_amount,
            $order->delivery_date->format('d/m/Y'),
            $order->client_validation_code
        );

        // Notifier tous les utilisateurs actifs (sauf clients) d'une nouvelle commande
        $notificationService = app(NotificationService::class);
        $recipients = User::where('role', '!=', 'client')->get();
        foreach ($recipients as $recipient) {
            $notificationService->notify(
                $recipient,
                'order',
                'Nouvelle commande créée',
                "L'agent {$order->agent->name} a créé la commande {$order->code}. Ouvrez-la pour prendre des dispositions.",
                'order_created',
                $recipient->isAdmin() ? route('admin.orders.show', $order) : route('dashboard'),
            );
        }

        app(ActivityLogService::class)->logFromRequest($request, 'order_created', Order::class, $order->id, 'Agent created order '.$order->code);

        // Envoyer un email au client si un compte existe avec une adresse email valide
        if ($order->client && filter_var($order->client->email, FILTER_VALIDATE_EMAIL)) {
            try {
                $order->client->notify(new OrderCreatedClientMail($order));
            } catch (\Throwable $e) {
                Log::warning('Order created email failed.', [
                    'order_id' => $order->id,
                    'client_id' => $order->client_id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return redirect()->route('agent.orders.index')
            ->with('success', 'Commande enregistrée.')
            ->with('whatsapp_link', $whatsappLink);
    }

    public function show(Request $request, Order $order)
    {
        abort_unless($order->agent_id === $request->user()->id, 403);

        $order->load(['items.meal', 'client']);

        return view('agent.orders.show', compact('order'));
    }
}
