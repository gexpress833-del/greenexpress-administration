<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

Route::get('/clear-cache', function () {
    try {
        Artisan::call('optimize:clear');
        Artisan::call('view:clear');
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('route:clear');
        return '<h1>Caches cleared!</h1><p>All caches have been cleared successfully.</p>';
    } catch (\Throwable $e) {
        return '<h1>Error</h1><p>' . $e->getMessage() . '</p>';
    }
});
