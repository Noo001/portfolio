<?php

declare(strict_types=1);

namespace App\Tests\Services;

use App\Services\RateLimitService;
use App\Tests\Fakes\FakeRateLimitRepository;
use PHPUnit\Framework\TestCase;

class RateLimitServiceTest extends TestCase
{
    public function testAllowFirstRequest(): void
    {
        $service = new RateLimitService(new FakeRateLimitRepository(), 5, 60);

        $this->assertTrue($service->allow('127.0.0.1'));
    }

    public function testAllowUpToMaxRequests(): void
    {
        $service = new RateLimitService(new FakeRateLimitRepository(), 3, 60);

        $this->assertTrue($service->allow('127.0.0.1'));
        $this->assertTrue($service->allow('127.0.0.1'));
        $this->assertTrue($service->allow('127.0.0.1'));
    }

    public function testDenyAfterMaxRequests(): void
    {
        $service = new RateLimitService(new FakeRateLimitRepository(), 2, 60);

        $this->assertTrue($service->allow('127.0.0.1'));
        $this->assertTrue($service->allow('127.0.0.1'));
        $this->assertFalse($service->allow('127.0.0.1'));
    }

    public function testGetRemaining(): void
    {
        $service = new RateLimitService(new FakeRateLimitRepository(), 5, 60);

        $this->assertSame(5, $service->getRemaining('127.0.0.1'));
        $service->allow('127.0.0.1');
        $this->assertSame(4, $service->getRemaining('127.0.0.1'));
    }

    public function testDifferentIdentifiersAreIndependent(): void
    {
        $service = new RateLimitService(new FakeRateLimitRepository(), 1, 60);

        $this->assertTrue($service->allow('127.0.0.1'));
        $this->assertFalse($service->allow('127.0.0.1'));
        $this->assertTrue($service->allow('192.168.1.1'));
    }
}
