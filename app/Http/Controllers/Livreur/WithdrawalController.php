<?php

namespace App\Http\Controllers\Livreur;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Withdrawal;
use App\Notifications\WithdrawalRequested;
use App\Services\PointService;
use App\Services\PointWithdrawalService;
use Illuminate\Http\Request;

class WithdrawalController extends Controller
{
    public function index(Request $request, PointWithdrawalService $withdrawalService)
    {
        $user = $request->user();
        $withdrawals = Withdrawal::where('user_id', $user->id)->latest()->paginate(15);
        $availablePoints = $withdrawalService->availablePoints($user);
        $available = round($availablePoints * PointService::VALUE_PER_POINT_USD, 2);

        return view('livreur.withdrawals.index', compact('withdrawals', 'availablePoints', 'available'));
    }

    public function store(Request $request, PointWithdrawalService $withdrawalService)
    {
        $user = $request->user();
        $data = $request->validate([
            'points' => ['required', 'integer', 'min:1'],
            'mobile_money_operator' => ['required', 'string', 'max:100'],
            'mobile_money_number' => ['required', 'string', 'max:30'],
        ]);

        $withdrawal = $withdrawalService->create(
            $user,
            $data['points'],
            $data['mobile_money_operator'],
            $data['mobile_money_number'],
        );

        $withdrawal->load('user');
        try {
            $user->notify(new WithdrawalRequested($withdrawal));
            User::where('role', 'admin')->get()->each(fn (User $admin) => $admin->notify(new WithdrawalRequested($withdrawal)));
        } catch (\Throwable $exception) {
            report($exception);
        }

        return redirect()->route('livreur.withdrawals.index')->with('success', 'Demande de retrait de points envoyée.');
    }
}
