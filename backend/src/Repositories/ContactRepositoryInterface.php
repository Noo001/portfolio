<?php

declare(strict_types=1);

namespace App\Repositories;

interface ContactRepositoryInterface
{
    /**
     * @param array<string, mixed> $data
     */
    public function save(array $data): void;

    /**
     * @return array<int, array<string, mixed>>
     */
    public function all(): array;
}
