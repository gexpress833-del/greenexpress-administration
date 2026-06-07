<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;

class ComplaintController extends Controller
{
    public function index(Request $request)
    {
        $complaints = Complaint::with(['order', 'client'])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = '%' . $request->search . '%';
                $query->where(function ($q) use ($search) {
                    $q->where('description', 'like', $search)
                        ->orWhereHas('client', fn ($cq) => $cq->where('name', 'like', $search))
                        ->orWhereHas('order', fn ($oq) => $oq->where('code', 'like', $search));
                });
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.complaints.index', compact('complaints'));
    }

    public function show(Request $request, Complaint $complaint)
    {
        $complaint->load(['order.items.meal', 'client', 'resolver']);
        return view('admin.complaints.show', compact('complaint'));
    }

    public function update(Request $request, Complaint $complaint)
    {
        $data = $request->validate([
            'status' => ['required', 'in:open,in_progress,resolved,rejected'],
            'admin_response' => ['nullable', 'string', 'max:1000'],
        ]);

        $complaint->status = $data['status'];
        $complaint->admin_response = $data['admin_response'];

        if (in_array($data['status'], ['resolved', 'rejected']) && ! $complaint->resolved_at) {
            $complaint->resolved_by = $request->user()->id;
            $complaint->resolved_at = now();
        }

        $complaint->save();

        app(ActivityLogService::class)->logFromRequest($request, 'complaint_updated', Complaint::class, $complaint->id, 'Admin updated complaint #' . $complaint->id . ' to status ' . $complaint->status);

        return redirect()->route('admin.complaints.show', $complaint)
            ->with('success', 'Réclamation mise à jour.');
    }
}
