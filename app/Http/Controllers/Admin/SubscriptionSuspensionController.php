<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionSuspension;
use App\Notifications\SubscriptionSuspensionProcessed;
use Illuminate\Http\Request;

class SubscriptionSuspensionController extends Controller
{
    public function index()
    {
        $suspensions = SubscriptionSuspension::with(['subscription.client'])->latest()->paginate(20);

        return view('admin.suspensions.index', compact('suspensions'));
    }

    public function accept(Request $request, SubscriptionSuspension $suspension)
    {
        $suspension->status = 'accepted';
        $suspension->processed_by = $request->user()->id;
        $suspension->processed_at = now();
        $suspension->suspension_start = now();
        $suspension->suspension_end = now()->addDays($suspension->duration_days);
        $suspension->save();

        $subscription = $suspension->subscription;
        $subscription->status = 'suspended';
        $subscription->save();

        $subscription->client->notify(new SubscriptionSuspensionProcessed($suspension));

        return redirect()->route('admin.suspensions.index')->with('success', 'Suspension acceptée.');
    }

    public function reject(Request $request, SubscriptionSuspension $suspension)
    {
        $suspension->status = 'rejected';
        $suspension->processed_by = $request->user()->id;
        $suspension->processed_at = now();
        $suspension->save();

        $suspension->subscription->client->notify(new SubscriptionSuspensionProcessed($suspension));

        return redirect()->route('admin.suspensions.index')->with('success', 'Suspension rejetée.');
    }
}
