<?php

declare(strict_types= 1);

namespace Marshal\Platform\Web;

use Fig\Http\Message\StatusCodeInterface;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\JsonResponse;
use Marshal\Platform\PlatformInterface;
use Marshal\Platform\Web\Event\RenderTemplateEvent;
use Mezzio\Router\RouteResult;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class WebPlatform implements PlatformInterface
{
    public function __construct(private EventDispatcherInterface $eventDispatcher)
    {
    }

    public function formatResponse(
        ServerRequestInterface $request,
        array|\Traversable $data = [],
        int $status = StatusCodeInterface::STATUS_OK,
        array $headers = [],
        array $options = [],
        string $message = ""
    ): ResponseInterface {
        // json responses
        if (FALSE !== \strpos($request->getHeaderLine('accept'), 'application/json')) {
            return new JsonResponse($data, $status, $headers);
        }

        if ($status !== StatusCodeInterface::STATUS_OK) {
            switch ($status) {
                case StatusCodeInterface::STATUS_UNAUTHORIZED:
                    $template = "marshal::error-401";
                    break;

                case StatusCodeInterface::STATUS_SERVICE_UNAVAILABLE:
                    $template = "marshal::error-500";
                    break;

                case StatusCodeInterface::STATUS_NOT_FOUND:
                default:
                    $template = "marshal::error-404";
                    break;
            }
            $options['template'] = $template;
        }

        $template = $this->getTemplateName($request, $options);
        $event = new RenderTemplateEvent($template, $data);
        $this->eventDispatcher->dispatch($event);

        // return a html response
        return new HtmlResponse($event->getContents(), $status, $headers);
    }

    private function getTemplateName(ServerRequestInterface $request, array $options): string
    {
        if (isset($options['template']) && \is_string($options['template'])) {
            return $options['template'];
        }

        $routeResult = $request->getAttribute(RouteResult::class);
        if (! $routeResult instanceof RouteResult || $routeResult->isFailure()) {
            return "marshal::error-404";
        }

        $routeOptions = $routeResult->getMatchedRoute()->getOptions();
        if (! isset($routeOptions['template']) || ! \is_string($routeOptions['template'])) {
            return "marshal::error-404";
        }

        return $routeOptions['template'];
    }
}
