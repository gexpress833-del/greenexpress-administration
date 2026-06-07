<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\ExchangeRate;
use App\Models\Withdrawal;
use App\Services\CommissionService;
use Illuminate\Http\Request;

class WithdrawalController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $withdrawals = Withdrawal::where('agent_id', $user->id)->latest()->paginate(15);
        $available = app(CommissionService::class)->getAvailableBalance($user->id);

        return view('agent.withdrawals.index', compact('withdrawals', 'available'));
    }

    public function store(Request $request)
    {
        $user = $request->user();
        $available = app(CommissionService::class)->getAvailableBalance($user->id);
        $minWithdrawal = CommissionService::MIN_WITHDRAWAL_USD;

        $data = $request->validate([
            'amount_usd' => ['required', 'numeric', "min:{$minWithdrawal}", "max:{$available}"],
        ]);

        Withdrawal::create([
            'agent_id' => $user->id,
            'amount_usd' => $data['amount_usd'],
            'amount_fc' => $data['amount_usd'] * ExchangeRate::current(),
            'status' => 'pending',
        ]);

        return redirect()->route('agent.withdrawals.index')->with('success', 'Demande de retrait envoyée.');
    }
}
