<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\ExchangeRate;
use App\Models\User;
use App\Models\Withdrawal;
use App\Notifications\WithdrawalRequested;
use App\Services\CurrencyService;
use App\Services\PointService;
use Illuminate\Http\Request;

class WithdrawalController extends Controller
{
    public function index(Request $request)
    {
        try {
            $user = $request->user();
            $withdrawals = Withdrawal::where('agent_id', $user->id)->latest()->paginate(15);
            $pointService = app(PointService::class);
            $available = $pointService->getAvailableBalance($user->id);
            $minWithdrawal = PointService::MIN_WITHDRAWAL_USD;
            $currencyService = new CurrencyService();
            $exchangeRate = $currencyService->getRate();
            $minWithdrawalFc = $currencyService->usdToFc($minWithdrawal);
            $availableFc = $currencyService->usdToFc($available);
            $totalValue = $pointService->getTotalValueUsd($user->id);
            $totalValueFc = $currencyService->usdToFc($totalValue);
            $totalWithdrawn = $pointService->getTotalWithdrawn($user->id);
            $totalWithdrawnFc = $currencyService->usdToFc($totalWithdrawn);

            return view('agent.withdrawals.index', compact(
                'withdrawals', 'available', 'availableFc', 'minWithdrawal', 'minWithdrawalFc',
                'totalValue', 'totalValueFc', 'totalWithdrawn', 'totalWithdrawnFc', 'exchangeRate'
            ));
        } catch (\Throwable $e) {
            return response('<h1>Debug Error</h1><pre>' . htmlspecialchars($e->getMessage()) . "\n\n" . htmlspecialchars($e->getTraceAsString()) . '</pre>', 500);
        }
    }

    public function store(Request $request)
    {
        $user = $request->user();
        $available = app(PointService::class)->getAvailableBalance($user->id);
        $minWithdrawal = PointService::MIN_WITHDRAWAL_USD;
        $exchangeRate = ExchangeRate::current();

        $minFc = round($minWithdrawal * $exchangeRate, 2);
        $maxFc = round($available * $exchangeRate, 2);

        $data = $request->validate([
            'currency' => ['required', 'in:usd,fc'],
            'amount_usd' => ['nullable', 'numeric', 'required_if:currency,usd', 'min:' . $minWithdrawal, 'max:' . $available],
            'amount_fc'  => ['nullable', 'numeric', 'required_if:currency,fc', 'min:' . $minFc, 'max:' . $maxFc],
        ]);

        if ($data['currency'] === 'fc') {
            $amountFc = round((float) $data['amount_fc'], 2);
            $amountUsd = round($amountFc / $exchangeRate, 2);
        } else {
            $amountUsd = round((float) $data['amount_usd'], 2);
            $amountFc = round($amountUsd * $exchangeRate, 2);
        }

        $withdrawal = Withdrawal::create([
            'agent_id' => $user->id,
            'amount_usd' => $amountUsd,
            'amount_fc' => $amountFc,
            'status' => 'pending',
        ]);

        $withdrawal->load('agent');
        try {
            $user->notify(new WithdrawalRequested($withdrawal));
            User::where('role', 'admin')->get()->each(fn ($admin) => $admin->notify(new WithdrawalRequested($withdrawal)));
        } catch (\Throwable $e) {
            \Log::error('Failed sending withdrawal notifications: ' . $e->getMessage());
        }

        return redirect()->route('agent.withdrawals.index')->with('success', 'Demande de retrait envoyée.');
    }
}
