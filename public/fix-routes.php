<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle($request = Illuminate\Http\Request::capture());

// Supprimer le fichier de cache des routes s'il existe
$routeCache = __DIR__ . '/../bootstrap/cache/routes-v7.php';
if (file_exists($routeCache)) {
    unlink($routeCache);
    echo 'Deleted route cache file: routes-v7.php<br>';
} else {
    echo 'No route cache file found.<br>';
}

// Supprimer aussi d'autres fichiers de cache possibles
$cacheDir = __DIR__ . '/../bootstrap/cache';
$files = glob($cacheDir . '/*.php');
$count = 0;
foreach ($files as $file) {
    if (basename($file) !== '.gitignore') {
        unlink($file);
        $count++;
    }
}
echo 'Deleted ' . $count . ' cache files from bootstrap/cache.<br>';

// Vider les caches Laravel
Artisan::call('route:clear');
echo 'Route cache cleared.<br>';

Artisan::call('optimize:clear');
echo 'Optimize cache cleared.<br>';

echo '<hr><strong>All caches cleared. Now reload /agent/withdrawals</strong>';
