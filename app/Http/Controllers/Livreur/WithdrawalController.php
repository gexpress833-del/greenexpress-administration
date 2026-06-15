<?php

namespace App\Http\Controllers\Livreur;

use App\Http\Controllers\Controller;
use App\Models\ExchangeRate;
use App\Models\User;
use App\Models\Withdrawal;
use App\Notifications\WithdrawalRequested;
use App\Services\LivreurPointService;
use Illuminate\Http\Request;

class WithdrawalController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $withdrawals = Withdrawal::where('livreur_id', $user->id)->latest()->paginate(15);
        $available = app(LivreurPointService::class)->getAvailableBalance($user->id);

        return view('livreur.withdrawals.index', compact('withdrawals', 'available'));
    }

    public function store(Request $request)
    {
        $user = $request->user();
        $available = app(LivreurPointService::class)->getAvailableBalance($user->id);
        $minWithdrawal = LivreurPointService::MIN_WITHDRAWAL_USD;

        $data = $request->validate([
            'amount_usd' => ['required', 'numeric', "min:{$minWithdrawal}", "max:{$available}"],
        ]);

        $withdrawal = Withdrawal::create([
            'livreur_id' => $user->id,
            'agent_id' => null,
            'amount_usd' => $data['amount_usd'],
            'amount_fc' => $data['amount_usd'] * ExchangeRate::current(),
            'status' => 'pending',
        ]);

        $withdrawal->load('livreur');
        try {
            $user->notify(new WithdrawalRequested($withdrawal));
            User::where('role', 'admin')->get()->each(fn ($admin) => $admin->notify(new WithdrawalRequested($withdrawal)));
        } catch (\Throwable $e) {
            \Log::error('Failed sending livreur withdrawal notifications: ' . $e->getMessage());
        }

        return redirect()->route('livreur.withdrawals.index')->with('success', 'Demande de retrait envoyée.');
    }
}
