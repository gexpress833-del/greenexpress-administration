<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MenuController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\PlanController;
use App\Http\Controllers\Api\QuoteController;
use App\Http\Controllers\Api\SubscriptionController;
use Illuminate\Support\Facades\Route;

Route::prefix('api')->group(function () {
    // Route de test pour diagnostiquer l'API
    Route::get('/test', function () {
        return response()->json([
            'status' => 'ok',
            'message' => 'API is working',
            'timestamp' => now()->toIso8601String(),
            'environment' => app()->environment(),
        ]);
    });

    // Authentification - Public
    Route::post('/login', [AuthController::class, 'login']);

    // Routes publiques (accessibles sans authentification)
    Route::get('/menus', [MenuController::class, 'index']); // Liste des repas
    Route::get('/plans', [PlanController::class, 'index']); // Liste des plans d'abonnement

    // Routes protégées par sanctum (nécessitent authentification)
    Route::middleware('auth:sanctum')->group(function () {
        // Commandes
        Route::get('/orders', [OrderController::class, 'index']);
        Route::get('/orders/{id}', [OrderController::class, 'show']);

        // Abonnements (création et renouvellement)
        Route::post('/subscriptions', [SubscriptionController::class, 'store']);
        Route::post('/subscriptions/{id}/renew', [SubscriptionController::class, 'renew']);

        // Devis
        Route::post('/quotes', [QuoteController::class, 'store']);
    });
});
