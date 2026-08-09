<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MenuController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\PlanController;
use App\Http\Controllers\Api\QuoteController;
use App\Http\Controllers\Api\SubscriptionController;
use Illuminate\Support\Facades\Route;

Route::prefix('api')->group(function () {
    // Authentification
    Route::post('/login', [AuthController::class, 'login']);

    // Menus (Plans alimentaires) - Public
    Route::get('/menus', [MenuController::class, 'index']);

    // Plans (Types d'abonnement) - Public
    Route::get('/plans', [PlanController::class, 'index']);

    // Routes protégées par sanctum
    Route::middleware('auth:sanctum')->group(function () {
        // Commandes
        Route::get('/orders', [OrderController::class, 'index']);
        Route::get('/orders/{id}', [OrderController::class, 'show']);

        // Abonnements
        Route::post('/subscriptions', [SubscriptionController::class, 'store']);
        Route::post('/subscriptions/{id}/renew', [SubscriptionController::class, 'renew']);

        // Devis
        Route::post('/quotes', [QuoteController::class, 'store']);
    });
});
