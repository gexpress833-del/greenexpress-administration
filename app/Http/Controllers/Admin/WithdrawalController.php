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
        $withdrawals = Withdrawal::with('agent')->latest()->paginate(20);
        return view('admin.withdrawals.index', compact('withdrawals'));
    }

    public function update(Request $request, Withdrawal $withdrawal)
    {
        $request->validate(['status' => 'required|in:approved,rejected,paid']);
        $withdrawal->status = $request->status;
        $withdrawal->processed_by = $request->user()->id;
        $withdrawal->processed_at = now();
        $withdrawal->save();

        app(ActivityLogService::class)->logFromRequest($request, 'withdrawal_' . $request->status, Withdrawal::class, $withdrawal->id, 'Admin ' . $request->status . ' withdrawal #' . $withdrawal->id);

        $redirect = redirect()->route('admin.withdrawals.index')
            ->with('success', 'Statut mis à jour.');

        if (in_array($request->status, ['approved', 'paid']) && $withdrawal->agent->phone) {
            $whatsappLink = app(WhatsAppService::class)->withdrawalApprovedLink(
                $withdrawal->agent->phone,
                (float) $withdrawal->amount_usd
            );
            $redirect->with('whatsapp_link', $whatsappLink);
        }

        return $redirect;
    }
}
