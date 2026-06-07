<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Commission;
use Illuminate\Http\Request;

class CommissionController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $commissions = Commission::where('agent_id', $user->id)->latest()->paginate(15);
        $totalPoints = Commission::where('agent_id', $user->id)->sum('points');
        $totalUsd = Commission::where('agent_id', $user->id)->sum('amount_usd');
        $totalFc = Commission::where('agent_id', $user->id)->sum('amount_fc');

        return view('agent.commissions.index', compact('commissions', 'totalPoints', 'totalUsd', 'totalFc'));
    }
}
