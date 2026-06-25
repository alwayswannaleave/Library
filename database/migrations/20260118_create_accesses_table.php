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
        CREATE TABLE IF NOT EXISTS accesses (
            id INT AUTO_INCREMENT PRIMARY KEY,
            owner_user_id INT NOT NULL,
            granted_user_id INT NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (owner_user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (granted_user_id) REFERENCES users(id) ON DELETE CASCADE,
            UNIQUE KEY unique_access (owner_user_id, granted_user_id)
        )
    ';
    $pdo->exec($sql);

    echo "The \"accesses\" table has been successfully created{$config['database']}\n";
} catch (PDOException $e) {
    die('Failed migration: ' . $e->getMessage() . "\n");
}
