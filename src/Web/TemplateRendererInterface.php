<?php

declare(strict_types=1);

namespace Marshal\Platform\Web;

interface TemplateRendererInterface
{
    public function render(string $template, array|\Traversable $data, array $options = []): string;
}
