<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $logs = ActivityLog::with('user')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = '%' . $request->search . '%';
                $query->where(function ($q) use ($search) {
                    $q->where('action', 'like', $search)
                        ->orWhere('description', 'like', $search)
                        ->orWhere('model_type', 'like', $search)
                        ->orWhereHas('user', fn ($uq) => $uq->where('name', 'like', $search));
                });
            })
            ->latest()
            ->paginate(30)
            ->withQueryString();

        return view('admin.activity_logs.index', compact('logs'));
    }
}
