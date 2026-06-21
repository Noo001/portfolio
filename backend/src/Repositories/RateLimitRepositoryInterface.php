<?php

declare(strict_types=1);

namespace App\Repositories;

interface RateLimitRepositoryInterface
{
    public function get(string $identifier): array;

    public function save(string $identifier, array $data): void;
}
