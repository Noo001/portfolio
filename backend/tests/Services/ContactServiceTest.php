<?php

declare(strict_types=1);

namespace App\Tests\Services;

use App\Exceptions\ValidationException;
use App\Services\Ai\StubAiService;
use App\Services\ContactService;
use App\Services\EmailService;
use App\Services\MetricsService;
use App\Tests\Fakes\FakeContactRepository;
use App\Tests\Fakes\FakeMetricsRepository;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

class ContactServiceTest extends TestCase
{
    private ContactService $service;
    private FakeContactRepository $contactRepository;
    private FakeMetricsRepository $metricsRepository;

    protected function setUp(): void
    {
        $this->contactRepository = new FakeContactRepository();
        $this->metricsRepository = new FakeMetricsRepository();

        $this->service = new ContactService(
            new EmailService('', 587, '', '', '', '', '', new NullLogger()),
            new StubAiService(new NullLogger()),
            new MetricsService($this->metricsRepository),
            $this->contactRepository,
            new NullLogger()
        );
    }

    public function testProcessSuccess(): void
    {
        $result = $this->service->process([
            'name' => 'Иван',
            'phone' => '+7 999 123-45-67',
            'email' => 'ivan@example.com',
            'comment' => 'Хочу заказать сайт',
        ]);

        $this->assertTrue($result['success']);
        $this->assertSame('Письма успешно отправлены', $result['message']);
        $this->assertArrayHasKey('sentiment', $result);
        $this->assertCount(1, $this->contactRepository->saved);
        $this->assertSame('neutral', $this->contactRepository->saved[0]['sentiment']);
        $this->assertSame(1, $this->metricsRepository->metrics['total_contacts'] ?? 0);
    }

    public function testProcessValidationException(): void
    {
        $this->expectException(ValidationException::class);

        $this->service->process([
            'name' => '',
            'phone' => '123',
            'email' => 'invalid',
            'comment' => '',
        ]);
    }

    public function testProcessSanitizesInput(): void
    {
        $this->service->process([
            'name' => '<b>Иван</b>',
            'phone' => '+7 999 123-45-67',
            'email' => 'ivan@example.com',
            'comment' => '<script>alert("xss")</script>',
        ]);

        $saved = $this->contactRepository->saved[0];
        $this->assertStringNotContainsString('<script>', $saved['comment']);
        $this->assertStringContainsString('&lt;script&gt;', $saved['comment']);
    }
}
