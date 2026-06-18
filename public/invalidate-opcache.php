<?php

$files = [
    __DIR__ . '/../app/Http/Controllers/Agent/WithdrawalController.php',
    __DIR__ . '/../app/Http/Controllers/Livreur/WithdrawalController.php',
    __DIR__ . '/../routes/web.php',
    __DIR__ . '/../routes/cache-clear.php',
];

if (function_exists('opcache_invalidate')) {
    foreach ($files as $file) {
        if (file_exists($file)) {
            $result = opcache_invalidate($file, true);
            echo 'Invalidated: ' . $file . ' => ' . ($result ? 'OK' : 'FAILED') . "<br>\n";
        } else {
            echo 'Not found: ' . $file . "<br>\n";
        }
    }
} else {
    echo 'opcache_invalidate not available.<br>';
}

if (function_exists('opcache_reset')) {
    opcache_reset();
    echo 'OPcache reset OK.<br>';
}

echo '<hr><strong>Done. Reload /agent/withdrawals</strong>';
