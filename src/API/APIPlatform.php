<?php

declare(strict_types= 1);

namespace Marshal\Platform\API;

use Laminas\Diactoros\Response\JsonResponse;
use Marshal\Platform\PlatformInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Fig\Http\Message\StatusCodeInterface;

class APIPlatform implements PlatformInterface
{
    public function formatResponse(
        ServerRequestInterface $request,
        array|\Traversable $data = [],
        int $status = StatusCodeInterface::STATUS_OK,
        array $headers = [],
        array $options = [],
        string $message = ""
    ): ResponseInterface {
        $response = new JsonResponse($data, $status, $headers);
        foreach ($headers as $name => $value) {
            $response = $response->withHeader($name, $value);
        }

        return $response;
    }

    public function getRoutes(): array
    {
        return [];
    }
}
