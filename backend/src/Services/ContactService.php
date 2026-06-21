<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\ValidationException;
use App\Repositories\ContactRepositoryInterface;
use App\Services\Ai\AiServiceInterface;
use App\Utils\Validator;
use Psr\Log\LoggerInterface;

class ContactService
{
    public function __construct(
        private readonly EmailService $emailService,
        private readonly AiServiceInterface $aiService,
        private readonly MetricsService $metricsService,
        private readonly ContactRepositoryInterface $contactRepository,
        private readonly LoggerInterface $logger
    ) {
    }

    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     * @throws ValidationException
     */
    public function process(array $data): array
    {
        $this->validate($data);

        $name = trim((string)$data['name']);
        $phone = trim((string)$data['phone']);
        $email = trim((string)$data['email']);
        $comment = trim((string)$data['comment']);

        $cleanData = [
            'name' => htmlspecialchars($name, ENT_QUOTES, 'UTF-8'),
            'phone' => htmlspecialchars($phone, ENT_QUOTES, 'UTF-8'),
            'email' => htmlspecialchars($email, ENT_QUOTES, 'UTF-8'),
            'comment' => htmlspecialchars($comment, ENT_QUOTES, 'UTF-8'),
        ];

        $this->logger->info('Получено новое сообщение', ['email' => $cleanData['email']]);

        // AI-анализ тональности
        $sentiment = $this->aiService->analyzeSentiment($cleanData['comment']);

        // Сохраняем в базу данных
        $this->contactRepository->save([
            ...$cleanData,
            'sentiment' => $sentiment['sentiment'] ?? 'neutral',
            'confidence' => $sentiment['confidence'] ?? 0.0,
        ]);

        // Отправка email
        try {
            $this->emailService->send($cleanData);
            $this->metricsService->recordSuccessfulEmail();
        } catch (\RuntimeException $e) {
            $this->metricsService->recordFailedEmail();
            throw $e;
        }

        $this->metricsService->recordContactRequest();

        return [
            'success' => true,
            'message' => 'Письма успешно отправлены',
            'sentiment' => $sentiment,
        ];
    }

    /**
     * @param array<string, mixed> $data
     * @throws ValidationException
     */
    private function validate(array $data): void
    {
        $errors = Validator::validate($data, [
            'name' => 'required',
            'phone' => 'required|phone',
            'email' => 'required|email',
            'comment' => 'required',
        ]);

        if (!empty($errors)) {
            throw new ValidationException(implode('; ', $errors));
        }
    }
}
