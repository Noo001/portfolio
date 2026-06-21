<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\MetricsRepositoryInterface;

class MetricsService
{
    public function __construct(private readonly MetricsRepositoryInterface $repository)
    {
    }

    public function recordContactRequest(): void
    {
        $this->repository->increment('total_contacts');
    }

    public function recordSuccessfulEmail(): void
    {
        $this->repository->increment('successful_emails');
    }

    public function recordFailedEmail(): void
    {
        $this->repository->increment('failed_emails');
    }

    public function recordAiRequest(): void
    {
        $this->repository->increment('ai_requests');
    }

    public function getMetrics(): array
    {
        return $this->repository->all();
    }
}
