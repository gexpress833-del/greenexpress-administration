<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle($request = Illuminate\Http\Request::capture());

// Vider le cache des vues Blade
$viewPath = __DIR__ . '/../storage/framework/views';
if (is_dir($viewPath)) {
    $files = glob($viewPath . '/*');
    $count = 0;
    foreach ($files as $file) {
        if (is_file($file) && basename($file) !== '.gitignore') {
            unlink($file);
            $count++;
        }
    }
    echo 'Cleared ' . $count . ' compiled view files.<br>';
} else {
    echo 'Views directory not found.<br>';
}

// Vider le cache Laravel
Artisan::call('cache:clear');
echo 'Cache cleared.<br>';

// Vider config
Artisan::call('config:clear');
echo 'Config cleared.<br>';

echo '<hr><strong>Done. Now try reloading /agent/withdrawals</strong>';
