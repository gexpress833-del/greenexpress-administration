<?php

$path = __DIR__.'/../database/database.sqlite';
if (! file_exists($path)) {
    echo "database file missing\n";
    exit(2);
}
try {
    $db = new PDO('sqlite:'.$path);
    $stmt = $db->prepare('SELECT count(*) FROM users WHERE email = ?');
    $stmt->execute(['admin@greenexpress.test']);
    $count = (int) $stmt->fetchColumn();
    echo $count > 0 ? "exists\n" : "missing\n";
} catch (Exception $e) {
    echo 'error: '.$e->getMessage()."\n";
    exit(1);
}
