<?php

declare(strict_types=1);

namespace App\Tests\Fakes;

use App\Repositories\RateLimitRepositoryInterface;

class FakeRateLimitRepository implements RateLimitRepositoryInterface
{
    /** @var array<string, array<string, mixed>> */
    public array $storage = [];

    public function get(string $identifier): array
    {
        return $this->storage[$identifier] ?? [];
    }

    public function save(string $identifier, array $data): void
    {
        $this->storage[$identifier] = $data;
    }
}
