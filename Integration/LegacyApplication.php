<?php

namespace Webfactory\Bundle\LegacyIntegrationBundle\Integration;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Webfactory\Dom\BaseParsingHelper;

class LegacyApplication extends IntegratableApplication {

    protected $bootstrapFile;
    protected $dispatched = false;
    protected $response;

    public function __construct($bootstrapFile, BaseParsingHelper $parser) {
        $this->bootstrapFile = $bootstrapFile;
        parent::__construct($parser);
    }

    public function isDispatched() {
        return $this->dispatched;
    }

    public function dispatch() {
        if (!$this->dispatched) {
            $this->dispatched = true;

            $legacyBootstrap = $this->bootstrapFile;

            $this->response = LegacyCaptureResponseFactory::create(function() use ($legacyBootstrap) {
                include($legacyBootstrap);
            });
        }
    }

    public function getResponse() {
        if ($this->response)
            return $this->response;

        throw new \Exception("Die Altanwendung hat noch keine Response generiert. Eventuell fehlt die Annotation /** @Dispatch */ an der aktuellen Controller-Action?");
    }

}
