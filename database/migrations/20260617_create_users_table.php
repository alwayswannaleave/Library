<?php

$config = require __DIR__ . '/../../config/database.php';

try {
    $pdo = new PDO(
        "mysql:host={$config['host']};port={$config['port']}",
        $config['username'],
        $config['password']
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $pdo->exec("CREATE DATABASE IF NOT EXISTS {$config['database']}");
    $pdo->exec("USE {$config['database']}");

    $sql = '
        CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            login VARCHAR(100) UNIQUE NOT NULL,
            password_hash VARCHAR(255) NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    ';
    $pdo->exec($sql);

    echo "The \"users\" table has been successfully created{$config['database']}\n";
} catch (PDOException $e) {
    die('Failed migration: ' . $e->getMessage() . "\n");
}
