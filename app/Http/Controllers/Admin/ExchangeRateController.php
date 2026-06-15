<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExchangeRate;
use Illuminate\Http\Request;

class ExchangeRateController extends Controller
{
    public function index()
    {
        $rates = ExchangeRate::latest()->paginate(20);
        $currentRate = ExchangeRate::current();
        return view('admin.exchange_rates.index', compact('rates', 'currentRate'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'rate' => ['required', 'numeric', 'min:1'],
        ]);

        ExchangeRate::create([
            'rate' => $data['rate'],
            'currency_from' => 'USD',
            'currency_to' => 'FC',
        ]);

        return redirect()->route('admin.exchange-rates.index')
            ->with('success', 'Taux de change mis à jour.');
    }
}
