<?php

declare(strict_types= 1);

namespace Marshal\Platform;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Fig\Http\Message\StatusCodeInterface;

interface PlatformInterface
{
    public function formatResponse(
        ServerRequestInterface $request,
        array|\Traversable $data = [],
        int $status = StatusCodeInterface::STATUS_OK,
        array $headers = [],
        array $options = [],
        string $message = ""
    ): ResponseInterface;
}
