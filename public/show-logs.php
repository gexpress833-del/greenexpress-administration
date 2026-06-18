<?php

$logFile = __DIR__ . '/../storage/logs/laravel.log';
if (file_exists($logFile)) {
    $content = file_get_contents($logFile);
    // Afficher les 8000 derniers caractères (dernières erreurs)
    echo '<pre style="font-family:monospace;font-size:12px;white-space:pre-wrap;">';
    echo htmlspecialchars(substr($content, -8000));
    echo '</pre>';
} else {
    echo 'No log file found at: ' . htmlspecialchars($logFile);
}
