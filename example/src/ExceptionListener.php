<?php

namespace TodoExample;

use RequestConverter\Exception\BadRequestException;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

class ExceptionListener
{
    /**
     * @var InvalidRequestRenderer
     */
    private $renderer;

    public function __construct(InvalidRequestRenderer $renderer)
    {
        $this->renderer = $renderer;
    }

    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $ex = $event->getException();

        if ($ex instanceof BadRequestException) {
            $event->setResponse($this->renderer->render($event->getRequest(), $ex));
        }
    }
}
