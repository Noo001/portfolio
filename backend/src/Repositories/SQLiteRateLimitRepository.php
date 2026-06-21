<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Database\Database;

class SQLiteRateLimitRepository implements RateLimitRepositoryInterface
{
    public function __construct(private readonly Database $database)
    {
    }

    public function get(string $identifier): array
    {
        $pdo = $this->database->getPdo();
        $stmt = $pdo->prepare('SELECT requests FROM rate_limits WHERE identifier = :identifier');
        $stmt->execute(['identifier' => $identifier]);

        $row = $stmt->fetch();

        if (!$row) {
            return [];
        }

        $requests = json_decode($row['requests'], true);

        return ['requests' => is_array($requests) ? $requests : []];
    }

    public function save(string $identifier, array $data): void
    {
        $pdo = $this->database->getPdo();
        $stmt = $pdo->prepare(
            'INSERT INTO rate_limits (identifier, requests) VALUES (:identifier, :requests)
             ON CONFLICT(identifier) DO UPDATE SET requests = :requests2, updated_at = CURRENT_TIMESTAMP'
        );

        $requestsJson = json_encode($data['requests'] ?? []);

        $stmt->execute([
            'identifier' => $identifier,
            'requests' => $requestsJson,
            'requests2' => $requestsJson,
        ]);
    }
}
