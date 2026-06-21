<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\RateLimitRepositoryInterface;

class RateLimitService
{
    public function __construct(
        private readonly RateLimitRepositoryInterface $repository,
        private readonly int $maxRequests,
        private readonly int $windowSeconds
    ) {
    }

    public function allow(string $identifier): bool
    {
        $now = time();
        $data = $this->repository->get($identifier);

        $requests = $data['requests'] ?? [];

        // Удаляем устаревшие запросы
        $requests = array_filter($requests, function ($timestamp) use ($now) {
            return $now - $timestamp < $this->windowSeconds;
        });

        if (count($requests) >= $this->maxRequests) {
            $this->repository->save($identifier, ['requests' => array_values($requests)]);
            return false;
        }

        $requests[] = $now;
        $this->repository->save($identifier, ['requests' => array_values($requests)]);

        return true;
    }

    public function getRemaining(string $identifier): int
    {
        $now = time();
        $data = $this->repository->get($identifier);
        $requests = $data['requests'] ?? [];

        $requests = array_filter($requests, function ($timestamp) use ($now) {
            return $now - $timestamp < $this->windowSeconds;
        });

        return max(0, $this->maxRequests - count($requests));
    }

    public function getMaxRequests(): int
    {
        return $this->maxRequests;
    }
}
