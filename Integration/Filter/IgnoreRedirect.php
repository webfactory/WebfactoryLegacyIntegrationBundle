<?php

namespace Webfactory\Bundle\LegacyIntegrationBundle\Integration\Filter;

use Symfony\Component\HttpFoundation\Response;

class IgnoreRedirect extends PassthruLegacyResponseFilter {

    protected function check(Response $response) {
        return $response->isRedirect();
    }

}
