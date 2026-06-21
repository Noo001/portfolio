<?php

declare(strict_types=1);

namespace App\Tests\Services;

use App\Services\EmailService;
use App\Tests\Fakes\FakeLogger;
use PHPUnit\Framework\TestCase;

class EmailServiceTest extends TestCase
{
    public function testSendWhenNotConfiguredLogsWarning(): void
    {
        $logger = new FakeLogger();
        $service = new EmailService('', 587, '', '', '', '', '', $logger);

        $service->send([
            'name' => 'Иван',
            'phone' => '+7 999 123-45-67',
            'email' => 'ivan@example.com',
            'comment' => 'Привет',
        ]);

        $this->assertTrue($logger->hasLevel('warning'));
        $this->assertTrue($logger->hasMessageContaining('SMTP не настроен'));
    }

    public function testSendWhenConfiguredWithoutPasswordLogsWarning(): void
    {
        $logger = new FakeLogger();
        $service = new EmailService('smtp.gmail.com', 587, 'user@gmail.com', '', '', '', 'owner@example.com', $logger);

        $service->send([
            'name' => 'Иван',
            'phone' => '+7 999 123-45-67',
            'email' => 'ivan@example.com',
            'comment' => 'Привет',
        ]);

        $this->assertTrue($logger->hasLevel('warning'));
        $this->assertTrue($logger->hasMessageContaining('SMTP не настроен'));
    }
}
