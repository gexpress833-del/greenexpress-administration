<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class DocumentVerificationController extends Controller
{
    public function show(Request $request)
    {
        $code = $request->query('code');
        $order = null;

        if ($code) {
            $order = Order::where('code', $code)->with('agent')->first();
        }

        return view('verify', compact('order', 'code'));
    }
}
