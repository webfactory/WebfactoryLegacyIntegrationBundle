<?php

namespace Webfactory\Bundle\LegacyIntegrationBundle\Twig;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Webfactory\Bundle\LegacyIntegrationBundle\Integration\LegacyApplication;

class Extension extends \Twig_Extension {

    protected $legacyApplication;
    protected $container;

    public function __construct(ContainerInterface $container) {
        $this->container = $container;
    }

    public function getGlobals() {
        return array(
            'legacyApplication' => $this
        );
    }

    public function getName() {
        return 'webfactory.legacy_integration.twig_extension';
    }

    /** @deprecated */
    public function getFragmentalResponse() {
        return $this->getXPathHelper();
    }

    public function xpath($xpath) {
        return $this->getXPathHelper()->getFragment($xpath);
    }

    protected function getXPathHelper() {
        return $this->container->get('webfactory.legacy_integration.xpath_helper');
    }
}
