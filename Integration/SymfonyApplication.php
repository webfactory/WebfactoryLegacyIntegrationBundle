<?php

namespace Webfactory\Bundle\LegacyIntegrationBundle\Integration;

use Symfony\Component\HttpFoundation\Response;

class SymfonyApplication extends IntegratableApplication {

    protected $response;

    public function setResponse(Response $response) {
        $this->response = $response;
    }

    public function getResponse() {
        if ($this->response)
            return $this->response;

        throw new \Exception("Die eigentliche Symfony-Response wurde noch nicht generiert!");
    }

}
