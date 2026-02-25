<?php

declare(strict_types=1);

namespace Marshal\Platform\Web\Listener;

use Marshal\Platform\Web\Template\TemplateRenderer;
use Psr\Container\ContainerInterface;

final class WebEventsListenerFactory
{
    public function __invoke(ContainerInterface $container): WebEventsListener
    {
        $renderer = $container->get(TemplateRenderer::class);
        \assert($renderer instanceof TemplateRenderer);

        return new WebEventsListener($renderer);
    }
}
