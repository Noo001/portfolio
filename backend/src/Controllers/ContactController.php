<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\ContactService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Psr7\Response;

class ContactController
{
    public function __construct(private readonly ContactService $contactService)
    {
    }

    public function send(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = $request->getParsedBody() ?? [];

        $result = $this->contactService->process($data);

        $response->getBody()->write(json_encode($result, JSON_UNESCAPED_UNICODE));

        return $response->withHeader('Content-Type', 'application/json');
    }
}
