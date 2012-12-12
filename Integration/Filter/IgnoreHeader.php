<?php

namespace Webfactory\Bundle\LegacyIntegrationBundle\Integration\Filter;

use Symfony\Component\HttpFoundation\Response;

class IgnoreHeader extends PassthruLegacyResponseFilter {

    protected $header;

    public function __construct($header) {
        $this->header = $header;
    }

    protected function check(Response $response) {
        return $response->headers->has($this->header);
    }

}
