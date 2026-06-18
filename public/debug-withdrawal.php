<?php

// Charger l'autoloader Laravel
require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Simuler une requête pour charger l'application
$kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

try {
    // Simuler l'appel au contrôleur
    $user = \App\Models\User::where('role', 'agent')->first();
    if (!$user) {
        echo 'No agent user found in DB.<br>';
    } else {
        echo 'User found: ' . $user->name . '<br>';
        
        $pointService = app(\App\Services\PointService::class);
        echo 'PointService loaded OK<br>';
        
        $available = $pointService->getAvailableBalance($user->id);
        echo 'Available balance: ' . $available . '<br>';
        
        $minWithdrawal = \App\Services\PointService::MIN_WITHDRAWAL_USD;
        echo 'Min withdrawal: ' . $minWithdrawal . '<br>';
        
        $currencyService = new \App\Services\CurrencyService();
        echo 'CurrencyService loaded OK<br>';
        
        $exchangeRate = $currencyService->getRate();
        echo 'Exchange rate: ' . $exchangeRate . '<br>';
        
        $availableFc = $currencyService->usdToFc($available);
        echo 'Available FC: ' . $availableFc . '<br>';
        
        $totalValue = $pointService->getTotalValueUsd($user->id);
        echo 'Total value: ' . $totalValue . '<br>';
        
        $totalWithdrawn = $pointService->getTotalWithdrawn($user->id);
        echo 'Total withdrawn: ' . $totalWithdrawn . '<br>';
        
        echo '<hr><strong>All checks passed! No error found.</strong>';
    }
} catch (\Throwable $e) {
    echo '<h1>Error Found</h1>';
    echo '<pre style="color:red;">';
    echo 'Message: ' . htmlspecialchars($e->getMessage()) . "\n";
    echo 'File: ' . htmlspecialchars($e->getFile()) . "\n";
    echo 'Line: ' . $e->getLine() . "\n\n";
    echo 'Trace:\n' . htmlspecialchars($e->getTraceAsString());
    echo '</pre>';
}
