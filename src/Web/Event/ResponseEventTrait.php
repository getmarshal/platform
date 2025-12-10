<?php

declare(strict_types=1);

namespace Marshal\Platform\Web\Event;

use Fig\Http\Message\StatusCodeInterface;

trait ResponseEventTrait
{
    private array $headers = [];
    private int $status = StatusCodeInterface::STATUS_OK;

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function setHeader(string $name, string $value): static
    {
        $this->headers[$name] = $value;
        return $this;
    }

    public function setHeaders(array $headers): static
    {
        $this->headers = $headers;
        return $this;
    }

    public function setStatus(int $status): static
    {
        $this->status = $status;
        return $this;
    }
}
