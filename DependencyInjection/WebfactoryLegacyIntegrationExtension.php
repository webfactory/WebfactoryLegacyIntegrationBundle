<?php

namespace Webfactory\Bundle\LegacyIntegrationBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

class WebfactoryLegacyIntegrationExtension extends Extension {

    public function load(array $configs, ContainerBuilder $container) {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('webfactory_legacy_integration.legacy_application_bootstrap_file', $config['legacyApplicationBootstrapFile']);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        switch (@$config['parsingMode']) {
            case 'html5':
                $container->setParameter('webfactory_legacy_integration.parser_class', 'Webfactory\Dom\PolyglotHTML5ParsingHelper');
                break;
            case 'xhtml10':
                $container->setParameter('webfactory_legacy_integration.parser_class', 'Webfactory\Dom\XHTML10ParsingHelper');
                break;
        }

        if (isset($config['legacyApplicationBootstrapFile'])) {

            $wrap = new Definition(
                'Webfactory\Bundle\LegacyIntegrationBundle\Integration\BootstrapFileKernelAdaptor',
                array($config['legacyApplicationBootstrapFile'])
            );

            $container
                    ->getDefinition('webfactory_legacy_integration.legacy_application')
                    ->addMethodCall('setLegacyKernel', array($wrap));

        }
    }

}
