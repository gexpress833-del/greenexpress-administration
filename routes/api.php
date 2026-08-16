<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CuisinierController;
use App\Http\Controllers\Api\ForgotPasswordController;
use App\Http\Controllers\Api\LivreurController;
use App\Http\Controllers\Api\MenuController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\PlanController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\QuoteController;
use App\Http\Controllers\Api\ResetPasswordController;
use App\Http\Controllers\Api\SecretaireController;
use App\Http\Controllers\Api\SubscriptionController;
use App\Http\Controllers\Api\UploadImageController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\VerificateurController;
use Illuminate\Support\Facades\Route;

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
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', ForgotPasswordController::class);
Route::post('/reset-password', ResetPasswordController::class);

// Routes publiques (accessibles sans authentification)
Route::get('/menus', [MenuController::class, 'index']); // Liste des repas
Route::get('/plans', [PlanController::class, 'index']); // Liste des plans d'abonnement

// Routes protégées par sanctum (nécessitent authentification)
Route::middleware('auth:sanctum')->group(function () {
    // Utilisateur connecté
    Route::get('/user', UserController::class);
    Route::put('/profile', [ProfileController::class, 'update']);
    Route::put('/profile/password', [ProfileController::class, 'updatePassword']);
    Route::post('/upload-image', UploadImageController::class);

    // Commandes
    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);

    // Abonnements (création et renouvellement)
    Route::post('/subscriptions', [SubscriptionController::class, 'store']);
    Route::post('/subscriptions/{id}/renew', [SubscriptionController::class, 'renew']);

    // Devis
    Route::post('/quotes', [QuoteController::class, 'store']);

    // Cuisinier
    Route::get('/cuisinier/stats', [CuisinierController::class, 'stats']);
    Route::get('/cuisinier/menus', [CuisinierController::class, 'menus']);
    Route::post('/cuisinier/menus', [CuisinierController::class, 'storeMenu']);
    Route::put('/cuisinier/menus/{id}', [CuisinierController::class, 'updateMenu']);
    Route::delete('/cuisinier/menus/{id}', [CuisinierController::class, 'destroyMenu']);
    Route::get('/cuisinier/orders', [CuisinierController::class, 'orders']);

    // Livreur
    Route::get('/livreur/stats', [LivreurController::class, 'stats']);
    Route::get('/livreur/assignments', [LivreurController::class, 'assignments']);
    Route::post('/livreur/validate-code', [LivreurController::class, 'validateCode']);
    Route::post('/driver/location', [LivreurController::class, 'updateLocation']);

    // Secrétaire
    Route::get('/secretaire/stats', [SecretaireController::class, 'stats']);
    Route::get('/secretaire/orders', [SecretaireController::class, 'orders']);
    Route::get('/secretaire/flux', [SecretaireController::class, 'flux']);

    // Vérificateur
    Route::get('/verificateur/stats', [VerificateurController::class, 'stats']);
    Route::get('/verificateur/history', [VerificateurController::class, 'history']);
    Route::post('/verificateur/validate-ticket', [VerificateurController::class, 'validateTicket']);
});
