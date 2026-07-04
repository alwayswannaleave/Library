<?php

$config = require __DIR__ . '/../../config/database.php';

try {
    $pdo = new PDO(
        "mysql:host={$config['host']};port={$config['port']}",
        $config['username'],
        $config['password']
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $pdo->exec("USE {$config['database']}");

    $sql = '
        CREATE TABLE IF NOT EXISTS logs (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NULL,
            action VARCHAR(100) NOT NULL,
            method VARCHAR(10) NULL,
            url TEXT NULL,
            ip_address VARCHAR(45) NULL,
            details JSON NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        
            INDEX idx_user_id (user_id),
            INDEX idx_action (action),
            INDEX idx_created_at (created_at)
        )
    ';
    $pdo->exec($sql);

    echo "The \"logs\" table has been successfully created{$config['database']}\n";
} catch (PDOException $e) {
    die('Failed migration: ' . $e->getMessage() . "\n");
}
