<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function exportSales(Request $request)
    {
        $this->authorize('viewAny', Order::class);

        $period = $request->query('period', 'month'); // day, week, month, year

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

        $orders = Order::whereBetween('created_at', [$start, $end])->get();

        $totalSales = $orders->sum('total');
        $ordersCount = $orders->count();

        $data = [
            'start' => $start,
            'end' => $end,
            'label' => $label,
            'totalSales' => $totalSales,
            'ordersCount' => $ordersCount,
            'generatedAt' => $now,
        ];

        $pdf = Pdf::loadView('admin.reports.sales_pdf', $data);

        $filename = sprintf('sales_report_%s_%s.pdf', $period, $now->format('Ymd_His'));
        return $pdf->download($filename);
    }
}
