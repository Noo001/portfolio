<?php

declare(strict_types=1);

namespace App\Tests\Services;

use App\Services\MetricsService;
use App\Tests\Fakes\FakeMetricsRepository;
use PHPUnit\Framework\TestCase;

class MetricsServiceTest extends TestCase
{
    public function testRecordContactRequest(): void
    {
        $repository = new FakeMetricsRepository();
        $service = new MetricsService($repository);

        $service->recordContactRequest();
        $service->recordContactRequest();

        $this->assertSame(2, $repository->metrics['total_contacts']);
    }

    public function testRecordSuccessfulEmail(): void
    {
        $repository = new FakeMetricsRepository();
        $service = new MetricsService($repository);

        $service->recordSuccessfulEmail();

        $this->assertSame(1, $repository->metrics['successful_emails']);
    }

    public function testRecordFailedEmail(): void
    {
        $repository = new FakeMetricsRepository();
        $service = new MetricsService($repository);

        $service->recordFailedEmail();

        $this->assertSame(1, $repository->metrics['failed_emails']);
    }

    public function testGetMetrics(): void
    {
        $repository = new FakeMetricsRepository();
        $service = new MetricsService($repository);

        $service->recordContactRequest();
        $service->recordAiRequest();

        $metrics = $service->getMetrics();

        $this->assertSame(1, $metrics['total_contacts']);
        $this->assertSame(1, $metrics['ai_requests']);
    }
}
