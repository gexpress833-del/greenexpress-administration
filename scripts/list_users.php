<?php

$path = __DIR__ . '/../database/database.sqlite';
if (!file_exists($path)) {
    echo "database file missing\n";
    exit(2);
}
try {
    $db = new PDO('sqlite:' . $path);
    $stmt = $db->query('SELECT id, name, email, role FROM users ORDER BY id');
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($rows as $r) {
        echo "{$r['id']}\t{$r['name']}\t{$r['email']}\t{$r['role']}\n";
    }
} catch (Exception $e) {
    echo 'error: ' . $e->getMessage() . "\n";
    exit(1);
}
