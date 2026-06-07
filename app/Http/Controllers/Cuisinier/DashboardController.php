<?php

namespace App\Http\Controllers\Cuisinier;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $todayOrders = Order::whereDate('created_at', today())->whereIn('status', ['confirmed', 'preparing', 'delivering'])->count();
        $pendingOrders = Order::where('status', 'confirmed')->count();
        $preparingOrders = Order::where('status', 'preparing')->count();
        $readyOrders = Order::where('status', 'delivering')->count();

        $recentOrders = Order::with(['items.meal', 'agent'])
            ->whereIn('status', ['confirmed', 'preparing', 'delivering'])
            ->latest()
            ->take(10)
            ->get();

        return view('cuisinier.dashboard', compact(
            'todayOrders',
            'pendingOrders',
            'preparingOrders',
            'readyOrders',
            'recentOrders'
        ));
    }
}
