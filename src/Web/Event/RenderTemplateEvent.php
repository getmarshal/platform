<?php

declare(strict_types=1);

namespace Marshal\Platform\Web\Event;

class RenderTemplateEvent
{
    private string $contents = "";

    public function __construct(private string $template, private array|\Traversable $data = [])
    {
    }

    public function getContents(): string
    {
        return $this->contents;
    }

    public function getData(): array|\Traversable
    {
        return $this->data;
    }

    public function getTemplate(): string
    {
        return $this->template;
    }

    public function setContents(string $contents): static
    {
        $this->contents = $contents;
        return $this;
    }
}
