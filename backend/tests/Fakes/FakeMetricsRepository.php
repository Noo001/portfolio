<?php

declare(strict_types=1);

namespace App\Tests\Fakes;

use App\Repositories\MetricsRepositoryInterface;

class FakeMetricsRepository implements MetricsRepositoryInterface
{
    /** @var array<string, int> */
    public array $metrics = [];

    public function increment(string $metric, int $value = 1): void
    {
        $this->metrics[$metric] = ($this->metrics[$metric] ?? 0) + $value;
    }

    public function all(): array
    {
        return $this->metrics;
    }
}
