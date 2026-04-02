<?php

declare(strict_types=1);

namespace Marshal\Platform\Web\TemplateRenderer\Twig;

use League\Flysystem\Filesystem;
use League\Flysystem\Local\LocalFilesystemAdapter;
use Marshal\Platform\Web\TemplateRenderer\TemplateRendererInterface;
use Psr\Container\ContainerInterface;
use Twig\Environment;
use Twig\Extension\DebugExtension;
use Twig\Loader\ArrayLoader;

class TwigTemplateRenderer implements TemplateRendererInterface
{
    public function __construct(
        private ContainerInterface $container,
        private array $config = [],
        private array $options = [],
        private array $templatesConfig = []
    ) {
    }

    public function render(string $template, array|\Traversable $data, array $options = []): string
    {
        $loaderData = ['__template' => $this->getTemplateContents($template)];
        foreach ($this->resolveTemplateIncludes($template) as $k) {
            $loaderData[$k] = $this->getTemplateContents($k);
        }

        // $loader = new ArrayLoader($loaderData);
        $twig = new Environment(new ArrayLoader($loaderData), $this->options);
        $twig->addExtension(new TwigExtension($this->config));
        if (isset($this->options['debug']) && TRUE === $this->options['debug']) {
            $twig->addExtension(new DebugExtension());
        }

        foreach ($this->config['runtime_loaders'] as $loader) {
            $twig->addRuntimeLoader($this->container->get($loader));
        }

        return $twig->render('__template', $data);
    }

    private function getTemplateContents(string $templateIdentifier): string
    {
        // get the directory and file
        $templateFileName = $this->templatesConfig[$templateIdentifier]['filename'];
        $split = \explode('/', $templateFileName);
        $filename = \array_pop($split);
        $dir = \implode('/', $split);

        // create the filesystem adapter
        $adapter = new LocalFilesystemAdapter($dir, lazyRootCreation: true);
        $filesystem = new Filesystem($adapter);

        // read the file
        $template = $filesystem->read($filename);
        if (! $template) {
            throw new \RuntimeException(\sprintf(
                "Template file %s not found",
                $templateFileName
            ));
        }

        return $this->parseResource($templateFileName, $template);
    }

    private function parseResource(string $resourceName, string $contents): string
    {
        if (false !== \mb_strpos($resourceName, '.twig')) {
            return $contents;
        }

        throw new \RuntimeException(\sprintf(
            "Could not parse resource %s",
            $resourceName
        ));
    }

    private function resolveTemplateIncludes(string $templateName): array
    {
        $includes = [];
        foreach ($this->templatesConfig[$templateName]['includes'] ?? [] as $include) {
            if (! isset($this->templatesConfig[$include])) {
                continue;
            }

            if (! isset($this->templatesConfig[$include]['filename'])) {
                continue;
            }

            $includes[] = $include;
            foreach ($this->resolveTemplateIncludes($include) as $subInclude) {
                $includes[] = $subInclude;
            }
        }

        return $includes;
    }
}
