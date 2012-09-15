<?php

namespace Webfactory\Bundle\LegacyIntegrationBundle\Integration\Filter;

use Webfactory\Bundle\LegacyIntegrationBundle\Integration\Filter as FilterInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpFoundation\Response;

class IgnoreRedirect implements FilterInterface {

    public function filter(FilterControllerEvent $event, Response $response) {
        if ($response->isRedirect()) {
            $event->setController(function() use ($response) {
                return $response;
            });
            $event->stopPropagation();
        }
    }

}