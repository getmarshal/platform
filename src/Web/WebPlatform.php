<?php

declare(strict_types= 1);

namespace Marshal\Platform\Web;

use Fig\Http\Message\StatusCodeInterface;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\JsonResponse;
use Marshal\Platform\PlatformInterface;
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
        ?string $message = null
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

        $event = new Event\RenderTemplateEvent($request, $data, $options);
        $this->eventDispatcher->dispatch($event);

        // return a html response
        return new HtmlResponse($event->getContents(), $status, $headers);
    }
}
