<?php

namespace Webfactory\Bundle\LegacyIntegrationBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Webfactory\Bundle\LegacyIntegrationBundle\DependencyInjection\Compiler\IntegrationFilterPass;

class WebfactoryLegacyIntegrationBundle extends Bundle {

    public function build(ContainerBuilder $container) {
        parent::build($container);
        $container->addCompilerPass(new IntegrationFilterPass());
    }

}
