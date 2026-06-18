<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle($request = Illuminate\Http\Request::capture());

try {
    $user = \App\Models\User::where('role', 'agent')->first();
    if (!$user) {
        echo 'No agent user found.<br>';
        exit;
    }
    
    Auth::login($user);
    
    $pointService = app(\App\Services\PointService::class);
    $available = $pointService->getAvailableBalance($user->id);
    $minWithdrawal = \App\Services\PointService::MIN_WITHDRAWAL_USD;
    $currencyService = new \App\Services\CurrencyService();
    $exchangeRate = $currencyService->getRate();
    $minWithdrawalFc = $currencyService->usdToFc($minWithdrawal);
    $availableFc = $currencyService->usdToFc($available);
    $totalValue = $pointService->getTotalValueUsd($user->id);
    $totalValueFc = $currencyService->usdToFc($totalValue);
    $totalWithdrawn = $pointService->getTotalWithdrawn($user->id);
    $totalWithdrawnFc = $currencyService->usdToFc($totalWithdrawn);
    $withdrawals = \App\Models\Withdrawal::where('agent_id', $user->id)->latest()->paginate(15);
    
    $html = view('agent.withdrawals.index', compact(
        'withdrawals', 'available', 'availableFc', 'minWithdrawal', 'minWithdrawalFc',
        'totalValue', 'totalValueFc', 'totalWithdrawn', 'totalWithdrawnFc', 'exchangeRate'
    ))->render();
    
    echo '<h2>View rendered successfully!</h2>';
    echo 'Length: ' . strlen($html) . ' chars<br>';
    echo '<hr>';
    echo $html;
    
} catch (\Throwable $e) {
    echo '<h1 style="color:red;">View Render Error</h1>';
    echo '<pre style="color:red;font-family:monospace;">';
    echo 'Message: ' . htmlspecialchars($e->getMessage()) . "\n";
    echo 'File: ' . htmlspecialchars($e->getFile()) . ':' . $e->getLine() . "\n\n";
    echo 'Trace:\n' . htmlspecialchars($e->getTraceAsString());
    echo '</pre>';
}
