<?php

namespace Webfactory\Bundle\LegacyIntegrationBundle\Integration;

use Webfactory\Bundle\LegacyIntegrationBundle\Integration\FragmentalResponse;

abstract class IntegratableApplication {

    abstract public function getResponse();

    public function getFragmentalResponse() {
        $response = $this->getResponse();
        return new FragmentalResponse(
            $response->getContent(),
            $response->getStatusCode(),
            $response->headers->all()
        );
    }

}

