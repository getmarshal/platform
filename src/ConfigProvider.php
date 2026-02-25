<?php

declare(strict_types=1);

namespace Marshal\Platform;

final class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            "dependencies" => $this->getDependencies(),
            "events" => $this->getEventsConfig(),
            "twig" => $this->getTwigConfig(),
        ];
    }

    private function getDependencies(): array
    {
        return [
            "factories" => [
                PlatformMiddleware::class                              => PlatformMiddlewareFactory::class,
                API\APIPlatform::class                                 => API\APIPlatformFactory::class,
                Web\Listener\WebEventsListener::class                  => Web\Listener\WebEventsListenerFactory::class,
                Web\Template\RuntimeLoader::class                      => Web\Template\RuntimeLoaderFactory::class,
                Web\Template\TemplateRenderer::class                   => Web\Template\TemplateRendererFactory::class,
                Web\WebPlatform::class                                 => Web\WebPlatformFactory::class,
            ],
        ];
    }

    private function getEventsConfig(): array
    {
        return [
            "listeners" => [
                Web\Listener\WebEventsListener::class => [
                    Web\Event\RenderTemplateEvent::class => [
                        "listener" => "onRenderTemplateEvent",
                    ],
                ],
            ]
        ];
    }

    private function getTwigConfig(): array
    {
        return [
            "runtime_loaders" => [
                Web\Template\RuntimeLoader::class,
            ],
            "functions" => [
                [
                    "name" => "media",
                    "callable" => [Web\Template\UrlExtension::class, "media"],
                    "options" => [
                        "needs_context" => true,
                    ],
                ],
                [
                    "name" => "path",
                    "callable" => [Web\Template\UrlExtension::class, "path"],
                ],
                [
                    "name" => "static",
                    "callable" => [Web\Template\UrlExtension::class, "static"],
                    "options" => [
                        "needs_context" => true,
                        "needs_environment" => true,
                    ],
                ],
            ],
        ];
    }
}
