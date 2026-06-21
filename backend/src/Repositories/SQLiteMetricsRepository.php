<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Database\Database;

class SQLiteMetricsRepository implements MetricsRepositoryInterface
{
    public function __construct(private readonly Database $database)
    {
    }

    public function increment(string $metric, int $value = 1): void
    {
        $pdo = $this->database->getPdo();

        $stmt = $pdo->prepare(
            'INSERT INTO metrics (metric, value) VALUES (:metric, :value)
             ON CONFLICT(metric) DO UPDATE SET value = value + :value2'
        );

        $stmt->execute([
            'metric' => $metric,
            'value' => $value,
            'value2' => $value,
        ]);
    }

    public function all(): array
    {
        $pdo = $this->database->getPdo();
        $stmt = $pdo->query('SELECT metric, value FROM metrics');
        $rows = $stmt->fetchAll();

        $result = [];
        foreach ($rows as $row) {
            $result[$row['metric']] = (int)$row['value'];
        }

        return $result;
    }
}
