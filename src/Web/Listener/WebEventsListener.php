<?php

declare(strict_types=1);

namespace Marshal\Platform\Web\Listener;

use loophp\collection\Collection;
use Marshal\Platform\Web\Template\TemplateRenderer;
use Marshal\Platform\Web\Event\RenderTemplateEvent;

class WebEventsListener
{
    public function __construct(private TemplateRenderer $renderer)
    {
    }

    public function onRenderTemplateEvent(RenderTemplateEvent $event): void
    {
        // prepare template data
        $data = [];
        foreach ($event->getData() as $key => $value) {
            if (\is_array($value)) {
                $data[$key] = $value;
            }

            if (\is_object($value) && \method_exists($value, 'toArray')) {
                $data[$key] = $value->toArray();
            }

            if ($value instanceof Collection) {
                $collection = [];
                foreach ($value as $row) {
                    if (\is_array($row)) {
                        $collection[] = $row;
                    }

                    if (\is_object($row) && \method_exists($row, 'toArray')) {
                        $collection[] = $row->toArray();
                    }
                }
                $data[$key] = $collection;
            }

            if (\is_scalar($value)) {
                $data[$key] = $value;
            }
        }

        // render the template
        $html = $this->renderer->render($event->getTemplate(), $data);
        $event->setContents($html);
    }
}
