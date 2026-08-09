<?php

namespace App\Http\Controllers\Api;

use App\Models\Quote;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class QuoteController
{
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email',
            'phone' => 'required|string',
            'message' => 'required|string',
            'subscription_type_id' => 'nullable|exists:subscription_types,id',
        ]);

        DB::beginTransaction();
        try {
            $quote = Quote::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'message' => $request->message,
                'subscription_type_id' => $request->subscription_type_id,
                'user_id' => Auth::id(),
                'status' => 'pending',
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Demande de devis envoyée avec succès',
                'data' => [
                    'id' => $quote->id,
                    'status' => $quote->status,
                ],
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
