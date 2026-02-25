<?php

declare(strict_types=1);

namespace Marshal\Platform\Web\Template;

use Marshal\Utils\FileSystem\Local\FileManager;
use Psr\Container\ContainerInterface;

final class TemplateRendererFactory
{
    public function __invoke(ContainerInterface $container): TemplateRenderer
    {
        $twigEnvironmentOptions = [
            'debug' => $container->get('config')['debug'] ?? FALSE,
            'use_yield' => TRUE,
        ];

        $config = $container->get('config')['twig'] ?? [];

        $templatesConfig = $container->get('config')['templates'] ?? [];

        $fileManager = $container->get(FileManager::class);
        \assert($fileManager instanceof FileManager);

        return new TemplateRenderer($container, $fileManager, $config, $twigEnvironmentOptions, $templatesConfig);
    }
}
