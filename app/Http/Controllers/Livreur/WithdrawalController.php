<?php

namespace App\Http\Controllers\Livreur;

use App\Http\Controllers\Controller;
use App\Models\ExchangeRate;
use App\Models\User;
use App\Models\Withdrawal;
use App\Notifications\WithdrawalRequested;
use App\Services\CurrencyService;
use App\Services\LivreurPointService;
use Illuminate\Http\Request;

class WithdrawalController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $withdrawals = Withdrawal::where('livreur_id', $user->id)->latest()->paginate(15);
        $pointService = app(LivreurPointService::class);
        $available = $pointService->getAvailableBalance($user->id);
        $minWithdrawal = LivreurPointService::MIN_WITHDRAWAL_USD;
        $currencyService = new CurrencyService();
        $exchangeRate = $currencyService->getRate();
        $minWithdrawalFc = $currencyService->usdToFc($minWithdrawal);
        $availableFc = $currencyService->usdToFc($available);
        $totalValue = $pointService->getTotalValueUsd($user->id);
        $totalValueFc = $currencyService->usdToFc($totalValue);
        $totalWithdrawn = $pointService->getTotalWithdrawn($user->id);
        $totalWithdrawnFc = $currencyService->usdToFc($totalWithdrawn);

        return view('livreur.withdrawals.index', compact(
            'withdrawals', 'available', 'availableFc', 'minWithdrawal', 'minWithdrawalFc',
            'totalValue', 'totalValueFc', 'totalWithdrawn', 'totalWithdrawnFc', 'exchangeRate'
        ));
    }

    public function store(Request $request)
    {
        $user = $request->user();
        $available = app(LivreurPointService::class)->getAvailableBalance($user->id);
        $minWithdrawal = LivreurPointService::MIN_WITHDRAWAL_USD;
        $exchangeRate = ExchangeRate::current();

        $data = $request->validate([
            'currency' => ['required', 'in:usd,fc'],
            'amount_usd' => ['nullable', 'numeric', 'required_if:currency,usd', "min:{$minWithdrawal}", "max:{$available}"],
            'amount_fc'  => ['nullable', 'numeric', 'required_if:currency,fc', "min:{$minWithdrawal * $exchangeRate}", "max:{$available * $exchangeRate}"],
        ]);

        if ($data['currency'] === 'fc') {
            $amountFc = round((float) $data['amount_fc'], 2);
            $amountUsd = round($amountFc / $exchangeRate, 2);
        } else {
            $amountUsd = round((float) $data['amount_usd'], 2);
            $amountFc = round($amountUsd * $exchangeRate, 2);
        }

        $withdrawal = Withdrawal::create([
            'livreur_id' => $user->id,
            'agent_id' => null,
            'amount_usd' => $amountUsd,
            'amount_fc' => $amountFc,
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
