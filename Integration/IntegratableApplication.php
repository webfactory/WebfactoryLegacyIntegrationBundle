<?php

namespace Webfactory\Bundle\LegacyIntegrationBundle\Integration;

use Webfactory\Bundle\LegacyIntegrationBundle\Integration\FragmentalResponse;
use Webfactory\Dom\BaseParser;

abstract class IntegratableApplication {

    protected $parser;

    public function __construct(BaseParser $parser) {
        $this->parser = $parser;
    }

    abstract public function getResponse();

    public function getFragmentalResponse() {
        $response = $this->getResponse();
        return new FragmentalResponse(
            $this->parser,
            $response->getContent(),
            $response->getStatusCode(),
            $response->headers->all()
        );
    }

}

