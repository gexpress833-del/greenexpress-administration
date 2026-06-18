<?php

$commands = [
    'php artisan route:list',
    'php artisan config:clear',
    'php artisan cache:clear',
    'php artisan view:clear',
    'php artisan optimize',
];

$baseDir = realpath(__DIR__ . '/..');

echo '<pre style="font-family:monospace;font-size:13px;line-height:1.4;">';

foreach ($commands as $cmd) {
    echo "========================================\n";
    echo "Running: $cmd\n";
    echo "========================================\n";

    $fullCmd = 'cd ' . escapeshellarg($baseDir) . ' && ' . $cmd . ' 2>&1';
    $output = shell_exec($fullCmd);

    if ($output === null) {
        echo "(no output or command failed)\n";
    } else {
        echo htmlspecialchars($output);
    }

    echo "\n\n";
}

echo "========================================\n";
echo "All commands executed.\n";
echo "========================================\n";
echo '</pre>';
