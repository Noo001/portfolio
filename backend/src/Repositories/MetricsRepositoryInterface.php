<?php

declare(strict_types=1);

namespace App\Repositories;

interface MetricsRepositoryInterface
{
    public function increment(string $metric, int $value = 1): void;

    public function all(): array;
}
