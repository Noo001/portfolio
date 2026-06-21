<?php

declare(strict_types=1);

namespace App\Database;

use PDO;
use PDOException;
use RuntimeException;

class Database
{
    private ?PDO $pdo = null;

    public function __construct(private readonly string $path)
    {
        $dir = dirname($this->path);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
    }

    public function getPdo(): PDO
    {
        if ($this->pdo === null) {
            try {
                $this->pdo = new PDO('sqlite:' . $this->path);
                $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                $this->pdo->exec('PRAGMA foreign_keys = ON;');
            } catch (PDOException $e) {
                throw new RuntimeException('Не удалось подключиться к SQLite: ' . $e->getMessage());
            }
        }

        return $this->pdo;
    }
}
