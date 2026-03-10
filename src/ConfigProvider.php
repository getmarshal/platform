<?php

declare(strict_types=1);

namespace Marshal\Platform;

final class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            "dependencies" => $this->getDependencies(),
        ];
    }

    private function getDependencies(): array
    {
        return [
            "factories" => [
                PlatformMiddleware::class                              => PlatformMiddlewareFactory::class,
                API\APIPlatform::class                                 => API\APIPlatformFactory::class,
                Web\WebPlatform::class                                 => Web\WebPlatformFactory::class,
            ],
        ];
    }
}
