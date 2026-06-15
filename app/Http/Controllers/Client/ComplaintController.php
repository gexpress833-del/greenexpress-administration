<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\Order;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;

class ComplaintController extends Controller
{
    public function index(Request $request)
    {
        $complaints = Complaint::where('client_id', $request->user()->id)
            ->with('order')
            ->latest()
            ->paginate(15);
        return view('client.complaints.index', compact('complaints'));
    }

    public function create(Request $request, Order $order)
    {
        abort_unless($order->client_id === $request->user()->id, 403);
        return view('client.complaints.create', compact('order'));
    }

    public function store(Request $request, Order $order)
    {
        abort_unless($order->client_id === $request->user()->id, 403);

        $data = $request->validate([
            'type' => ['required', 'in:missing_item,wrong_item,late_delivery,quality_issue,other'],
            'description' => ['required', 'string', 'max:1000'],
        ]);

        $complaint = Complaint::create([
            'order_id' => $order->id,
            'client_id' => $request->user()->id,
            'type' => $data['type'],
            'description' => $data['description'],
            'status' => 'open',
        ]);

        app(ActivityLogService::class)->logFromRequest($request, 'complaint_created', Complaint::class, $complaint->id, 'Client filed complaint for order ' . $order->code);

        return redirect()->route('client.complaints.index')
            ->with('success', 'Votre réclamation a été enregistrée.');
    }
}
