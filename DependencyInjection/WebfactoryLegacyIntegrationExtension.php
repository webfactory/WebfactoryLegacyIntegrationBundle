<?php

namespace Webfactory\Bundle\LegacyIntegrationBundle\DependencyInjection;

use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

class WebfactoryLegacyIntegrationExtension extends Extension {

    public function load(array $config, ContainerBuilder $container) {
        $container->setParameter('webfactory.legacy_integration.legacy_application_bootstrap_file', 'wfD2Engine.php');
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        if (false /** In der Konfiguration ist die "Recycling"-Integration gewählt */) {
            $container->removeDefinition('webfactory.legacy_integration.retrofitting_event_listener');
            $container->removeDefinition('webfactory.legacy_integration.symfony_application');
        } else {
            $container->removeDefinition('webfactory.legacy_integration.recycling_event_listener');
        }
    }

}
