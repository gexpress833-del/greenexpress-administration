<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Withdrawal;
use App\Services\ActivityLogService;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;

class WithdrawalController extends Controller
{
    public function index()
    {
        $withdrawals = Withdrawal::with(['user', 'agent'])->latest()->paginate(20);

        return view('admin.withdrawals.index', compact('withdrawals'));
    }

    public function update(Request $request, Withdrawal $withdrawal)
    {
        $request->validate(['status' => 'required|in:approved,rejected,paid']);

        $withdrawal->transitionTo($request->status, [
            'processed_by' => $request->user()->id,
            'processed_at' => now(),
        ]);

        app(ActivityLogService::class)->logFromRequest($request, 'withdrawal_'.$request->status, Withdrawal::class, $withdrawal->id, 'Admin '.$request->status.' withdrawal #'.$withdrawal->id);

        $redirect = redirect()->route('admin.withdrawals.index')
            ->with('success', 'Statut mis à jour.');

        $recipient = $withdrawal->user ?? $withdrawal->agent;
        if (in_array($request->status, ['approved', 'paid']) && $recipient?->phone) {
            $whatsappLink = app(WhatsAppService::class)->withdrawalApprovedLink(
                $recipient->phone,
                (float) $withdrawal->amount_usd
            );
            $redirect->with('whatsapp_link', $whatsappLink);
        }

        return $redirect;
    }
}
