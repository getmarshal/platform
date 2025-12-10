<?php

declare(strict_types= 1);

namespace Marshal\Platform\Web;

use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;

final class WebPlatformFactory
{
    public function __invoke(ContainerInterface $container): WebPlatform
    {
        $eventDispatcher = $container->get(EventDispatcherInterface::class);
        return new WebPlatform($eventDispatcher);
    }
}
