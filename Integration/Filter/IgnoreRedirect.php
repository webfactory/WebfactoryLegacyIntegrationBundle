<?php

namespace Webfactory\Bundle\LegacyIntegrationBundle\Integration\Filter;

use Symfony\Component\HttpFoundation\Response;

/**
 * Dieser Filter gibt die Response der Altanwendung zurück
 * (und führt den Symfony-Controller nicht aus), wenn die Altanwendung
 * einen Redirect generiert hat.
 */
class IgnoreRedirect extends PassthruLegacyResponseFilter {

    protected function check(Response $response) {
        return $response->isRedirect();
    }

}
