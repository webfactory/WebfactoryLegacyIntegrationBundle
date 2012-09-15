<?php

namespace Webfactory\Bundle\LegacyIntegrationBundle\Integration\Filter;

use Webfactory\Bundle\LegacyIntegrationBundle\Integration\Filter as FilterInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpFoundation\Response;

class IgnoreHeader implements FilterInterface {

    protected $header;

    public function __construct($header) {
        $this->header = $header;
    }

    public function filter(FilterControllerEvent $event, Response $response) {
        if ($response->headers->has($this->header)) {
            $event->setController(function() use ($response) {
                return $response;
            });
            $event->stopPropagation();
        }
    }

}