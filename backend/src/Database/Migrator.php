<?php

declare(strict_types=1);

namespace App\Database;

use PDO;

class Migrator
{
    public function __construct(private readonly Database $database)
    {
    }

    public function migrate(): void
    {
        $pdo = $this->database->getPdo();

        $pdo->exec('CREATE TABLE IF NOT EXISTS migrations (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            filename TEXT UNIQUE NOT NULL,
            executed_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );');

        $files = glob(__DIR__ . '/../../migrations/*.sql');
        sort($files);

        foreach ($files as $file) {
            $filename = basename($file);

            $stmt = $pdo->prepare('SELECT 1 FROM migrations WHERE filename = :filename');
            $stmt->execute(['filename' => $filename]);

            if ($stmt->fetch()) {
                continue;
            }

            $sql = file_get_contents($file);
            $pdo->exec($sql);

            $insert = $pdo->prepare('INSERT INTO migrations (filename) VALUES (:filename)');
            $insert->execute(['filename' => $filename]);
        }
    }
}
