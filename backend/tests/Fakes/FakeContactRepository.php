<?php

declare(strict_types=1);

namespace App\Tests\Fakes;

use App\Repositories\ContactRepositoryInterface;

class FakeContactRepository implements ContactRepositoryInterface
{
    /** @var array<int, array<string, mixed>> */
    public array $saved = [];

    public function save(array $data): void
    {
        $this->saved[] = $data;
    }

    public function all(): array
    {
        return $this->saved;
    }
}
