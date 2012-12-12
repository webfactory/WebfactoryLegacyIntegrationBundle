<?php

namespace Webfactory\Bundle\LegacyIntegrationBundle\Integration\Annotation;

use Symfony\Component\DependencyInjection\Container;
use Webfactory\Bundle\LegacyIntegrationBundle\Integration\Filter\Factory;
use Webfactory\Bundle\LegacyIntegrationBundle\Integration\Filter\PassthruLegacyResponseFilter;

/**
 * @Annotation
 */
class Passthru implements Factory {

    public function createFilter(Container $container) {
        return new PassthruLegacyResponseFilter();
    }

}
