<?php

declare(strict_types=1);

namespace App\Tests\Services\Ai;

use App\Services\Ai\StubAiService;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

class StubAiServiceTest extends TestCase
{
    private StubAiService $service;

    protected function setUp(): void
    {
        $this->service = new StubAiService(new NullLogger());
    }

    public function testAnalyzeSentimentPositive(): void
    {
        $result = $this->service->analyzeSentiment('Всё отлично, спасибо!');

        $this->assertSame('positive', $result['sentiment']);
        $this->assertGreaterThan(0, $result['confidence']);
    }

    public function testAnalyzeSentimentNegative(): void
    {
        $result = $this->service->analyzeSentiment('Всё плохо, я недоволен');

        $this->assertSame('negative', $result['sentiment']);
        $this->assertGreaterThan(0, $result['confidence']);
    }

    public function testAnalyzeSentimentNeutral(): void
    {
        $result = $this->service->analyzeSentiment('Хочу заказать сайт');

        $this->assertSame('neutral', $result['sentiment']);
        $this->assertGreaterThan(0, $result['confidence']);
    }

    public function testGenerateReply(): void
    {
        $reply = $this->service->generateReply('Тестовое сообщение');

        $this->assertNotEmpty($reply);
        $this->assertStringContainsString('Спасибо', $reply);
    }
}
