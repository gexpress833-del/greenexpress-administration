<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Subscription;
use App\Models\User;
use App\Models\Withdrawal;
use App\Services\StatisticsService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function exportSales(Request $request)
    {
        $period = $request->query('period', 'month');

        $now = Carbon::now();
        switch ($period) {
            case 'day':
                $start = $now->copy()->startOfDay();
                $end = $now->copy()->endOfDay();
                $label = 'Jour';
                break;
            case 'week':
                $start = $now->copy()->startOfWeek();
                $end = $now->copy()->endOfWeek();
                $label = 'Semaine';
                break;
            case 'year':
                $start = $now->copy()->startOfYear();
                $end = $now->copy()->endOfYear();
                $label = 'Année';
                break;
            case 'month':
            default:
                $start = $now->copy()->startOfMonth();
                $end = $now->copy()->endOfMonth();
                $label = 'Mois';
                break;
        }

        $orders = Order::with(['agent', 'client'])
            ->whereBetween('created_at', [$start, $end])
            ->orderBy('created_at')
            ->get();

        $totalSales = $orders->sum(fn ($o) => (float) $o->total_amount);
        $ordersCount = $orders->count();

        $data = [
            'start' => $start,
            'end' => $end,
            'label' => $label,
            'totalSales' => $totalSales,
            'ordersCount' => $ordersCount,
            'orders' => $orders,
            'generatedAt' => $now,
        ];

        $pdf = Pdf::loadView('admin.reports.sales_pdf', $data);

        return $pdf->download(sprintf('rapport_ventes_%s_%s.pdf', $period, $now->format('Ymd_His')));
    }

    public function exportStatistics(Request $request)
    {
        $start = $request->filled('start')
            ? Carbon::parse($request->query('start'))->startOfDay()
            : today()->subDays(30)->startOfDay();
        $end = $request->filled('end')
            ? Carbon::parse($request->query('end'))->endOfDay()
            : today()->endOfDay();

        $kpi = app(StatisticsService::class)->getDashboardKpi($start, $end);

        $totalClients = User::where('role', 'client')->count();
        $totalAgents = User::where('role', 'agent')->count();
        $totalLivreurs = User::where('role', 'livreur')->count();

        $ordersByStatus = Order::selectRaw('status, count(*) as count')
            ->whereBetween('created_at', [$start, $end])
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $data = [
            'kpi' => $kpi,
            'totalClients' => $totalClients,
            'totalAgents' => $totalAgents,
            'totalLivreurs' => $totalLivreurs,
            'ordersByStatus' => $ordersByStatus,
            'start' => $start,
            'end' => $end,
            'generatedAt' => Carbon::now(),
        ];

        $pdf = Pdf::loadView('admin.reports.statistics_pdf', $data);

        return $pdf->download(sprintf('statistiques_%s_%s.pdf', $start->format('Ymd'), $end->format('Ymd')));
    }

    public function exportFinancial(Request $request)
    {
        $start = $request->filled('start')
            ? Carbon::parse($request->query('start'))->startOfDay()
            : today()->startOfMonth()->startOfDay();
        $end = $request->filled('end')
            ? Carbon::parse($request->query('end'))->endOfDay()
            : today()->endOfDay();

        $validatedOrders = Order::where('status', 'delivered')
            ->whereNotNull('client_validated_at')
            ->whereBetween('client_validated_at', [$start, $end])
            ->with(['agent', 'items'])
            ->orderBy('client_validated_at')
            ->get();

        $totalRevenue = (float) $validatedOrders->sum(fn ($o) => (float) $o->total_amount);
        $totalRevenueFc = (float) $validatedOrders->sum(fn ($o) => (float) $o->total_amount_fc);

        $withdrawals = Withdrawal::whereBetween('created_at', [$start, $end])
            ->with(['user', 'agent'])
            ->orderBy('created_at')
            ->get();

        $withdrawalsPaid = (float) $withdrawals->whereIn('status', ['approved', 'paid'])->sum(fn ($w) => (float) $w->amount_usd);
        $withdrawalsPending = (float) $withdrawals->where('status', 'pending')->sum(fn ($w) => (float) $w->amount_usd);

        $subscriptionsRevenue = (float) Subscription::where('status', 'active')
            ->whereBetween('admin_validated_at', [$start, $end])
            ->sum('price');

        $totalIncome = $totalRevenue + $subscriptionsRevenue;
        $totalExpenses = $withdrawalsPaid;
        $netProfit = $totalIncome - $totalExpenses;

        $data = [
            'start' => $start,
            'end' => $end,
            'validatedOrders' => $validatedOrders,
            'totalRevenue' => $totalRevenue,
            'totalRevenueFc' => $totalRevenueFc,
            'withdrawals' => $withdrawals,
            'withdrawalsPaid' => $withdrawalsPaid,
            'withdrawalsPending' => $withdrawalsPending,
            'subscriptionsRevenue' => $subscriptionsRevenue,
            'totalIncome' => $totalIncome,
            'totalExpenses' => $totalExpenses,
            'netProfit' => $netProfit,
            'generatedAt' => Carbon::now(),
        ];

        $pdf = Pdf::loadView('admin.reports.financial_pdf', $data);

        return $pdf->download(sprintf('etats_financiers_%s_%s.pdf', $start->format('Ymd'), $end->format('Ymd')));
    }
}
