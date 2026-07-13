<?php

namespace App\Http\Controllers;

use App\Models\ExchangeRate;

class ExchangeRatePublicController extends Controller
{
    public function show()
    {
        $currentRate = ExchangeRate::current();

        $history = ExchangeRate::where('currency_from', 'USD')
            ->where('currency_to', 'FC')
            ->latest()
            ->take(7)
            ->get()
            ->reverse()
            ->values();

        return view('exchange-rate.show', compact('currentRate', 'history'));
    }
}
