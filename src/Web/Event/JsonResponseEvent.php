<?php

declare(strict_types=1);

namespace Marshal\Platform\Web\Event;

class JsonResponseEvent
{
    public function __construct(private array|\Traversable $data)
    {
    }

    public function getData(): array|\Traversable
    {
        return $this->data;
    }

    public function setData(array $data): static
    {
        $this->data = $data;
        return $this;
    }
}
