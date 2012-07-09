<?php

namespace Webfactory\Bundle\LegacyIntegrationBundle\Twig;

use Webfactory\Bundle\LegacyIntegrationBundle\Integration\LegacyApplication;

class Extension extends \Twig_Extension {

    protected $legacyApplication;

    public function __construct(LegacyApplication $legacyApplication) {
        $this->legacyApplication = $legacyApplication;
    }

    public function getGlobals() {
        return array(
            'legacyApplication' => $this->legacyApplication
        );
    }

    public function getName() {
        return 'webfactory_legacy_integration_extension';
    }

}