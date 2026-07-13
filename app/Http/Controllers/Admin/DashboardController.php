<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Subscription;
use App\Models\User;
use App\Models\Withdrawal;
use App\Services\StatisticsService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request, StatisticsService $statisticsService)
    {
        $filters = $request->validate([
            'start' => ['nullable', 'date'],
            'end' => ['nullable', 'date', 'after_or_equal:start'],
        ]);

        $start = isset($filters['start']) ? Carbon::parse($filters['start'])->startOfDay() : today()->subDays(30);
        $end = isset($filters['end']) ? Carbon::parse($filters['end'])->endOfDay() : today()->endOfDay();

        $kpi = $statisticsService->getDashboardKpi($start, $end);

        $totalClients = User::where('role', 'client')->count();
        $totalAgents = User::where('role', 'agent')->count();
        $totalLivreurs = User::where('role', 'livreur')->count();
        $pendingSubscriptions = Subscription::where('status', 'pending')->count();
        $pendingWithdrawals = Withdrawal::where('status', 'pending')->count();
        $todayOrders = Order::whereDate('created_at', today())->count();

        $ordersByStatus = Order::selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $recentOrders = Order::with(['agent', 'client'])->latest()->take(5)->get();
        $recentSubscriptions = Subscription::with(['client', 'agent'])->latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'kpi',
            'totalClients',
            'totalAgents',
            'totalLivreurs',
            'pendingSubscriptions',
            'pendingWithdrawals',
            'todayOrders',
            'ordersByStatus',
            'recentOrders',
            'recentSubscriptions',
            'start',
            'end'
        ));
    }
}
