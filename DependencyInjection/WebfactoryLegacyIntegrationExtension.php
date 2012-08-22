<?php

namespace Webfactory\Bundle\LegacyIntegrationBundle\DependencyInjection;

use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

class WebfactoryLegacyIntegrationExtension extends Extension {

    public function load(array $configs, ContainerBuilder $container) {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('webfactory.legacy_integration.legacy_application_bootstrap_file', $config['legacyApplicationBootstrapFile']);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        switch ($config['strategy']) {
            case 'recycling':
                $container->removeDefinition('webfactory.legacy_integration.retrofitting_event_listener');
                $container->removeDefinition('webfactory.legacy_integration.symfony_application');
                break;
            case 'retrofitting':
                $container->removeDefinition('webfactory.legacy_integration.recycling_event_listener');
                break;
            default:
                throw new \Symfony\Component\DependencyInjection\Exception\RuntimeException("Unbekannte Integrationsstrategie {$config['strategy']}.");
                break;
        }

        if (@$config['mode'] === 'html5') {
            $container->setParameter('webfactory.legacy_integration.parser.class', 'Webfactory\Dom\PolyglotHTML5Parser');
        }
    }

}
