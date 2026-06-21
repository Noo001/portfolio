<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Database\Database;

class SQLiteContactRepository implements ContactRepositoryInterface
{
    public function __construct(private readonly Database $database)
    {
    }

    public function save(array $data): void
    {
        $pdo = $this->database->getPdo();
        $stmt = $pdo->prepare(
            'INSERT INTO contacts (name, phone, email, comment, sentiment, confidence) 
             VALUES (:name, :phone, :email, :comment, :sentiment, :confidence)'
        );

        $stmt->execute([
            'name' => $data['name'],
            'phone' => $data['phone'],
            'email' => $data['email'],
            'comment' => $data['comment'],
            'sentiment' => $data['sentiment'] ?? null,
            'confidence' => $data['confidence'] ?? null,
        ]);
    }

    public function all(): array
    {
        $pdo = $this->database->getPdo();
        $stmt = $pdo->query('SELECT * FROM contacts ORDER BY created_at DESC');

        return $stmt->fetchAll();
    }
}
