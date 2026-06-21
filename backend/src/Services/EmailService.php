<?php

declare(strict_types=1);

namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use Psr\Log\LoggerInterface;

class EmailService
{
    public function __construct(
        private readonly string $host,
        private readonly int $port,
        private readonly string $username,
        private readonly string $password,
        private readonly string $fromEmail,
        private readonly string $fromName,
        private readonly string $ownerEmail,
        private readonly LoggerInterface $logger
    ) {
    }

    /**
     * @param array<string, string> $data
     */
    public function send(array $data): void
    {
        if (!$this->isConfigured()) {
            $this->logger->warning('SMTP не настроен. Сообщение сохранено в лог.', $data);
            return;
        }

        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = $this->host;
            $mail->SMTPAuth = true;
            $mail->Username = $this->username;
            $mail->Password = $this->password;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = $this->port;
            $mail->CharSet = PHPMailer::CHARSET_UTF8;

            $mail->setFrom($this->fromEmail, $this->fromName);
            $mail->addAddress($this->ownerEmail);
            $mail->addReplyTo($data['email'], $data['name']);

            $mail->isHTML(false);
            $mail->Subject = 'Новое сообщение с сайта портфолио';
            $mail->Body = $this->formatOwnerMessage($data);

            $mail->send();

            // Копия пользователю
            $mail->clearAddresses();
            $mail->addAddress($data['email']);
            $mail->Subject = 'Спасибо за ваше сообщение';
            $mail->Body = $this->formatUserMessage($data);
            $mail->send();

            $this->logger->info('Письма успешно отправлены', ['email' => $data['email']]);
        } catch (\Exception $e) {
            $this->logger->error('Ошибка отправки письма: ' . $e->getMessage());
            throw new \RuntimeException('Ошибка при отправке писем. Попробуйте позже.');
        }
    }

    private function isConfigured(): bool
    {
        return !empty($this->host)
            && !empty($this->username)
            && !empty($this->password)
            && !empty($this->ownerEmail);
    }

    /**
     * @param array<string, string> $data
     */
    private function formatOwnerMessage(array $data): string
    {
        return <<<TEXT
Получено новое сообщение с сайта портфолио.

Имя: {$data['name']}
Телефон: {$data['phone']}
Email: {$data['email']}
Сообщение:
{$data['comment']}
TEXT;
    }

    /**
     * @param array<string, string> $data
     */
    private function formatUserMessage(array $data): string
    {
        return <<<TEXT
Здравствуйте, {$data['name']}!

Спасибо за ваше сообщение. Мы получили его и скоро свяжемся с вами.

Ваше сообщение:
{$data['comment']}

С уважением,
Команда портфолио
TEXT;
    }
}
