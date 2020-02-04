<?php
/*
 * (c) webfactory GmbH <info@webfactory.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Webfactory\Bundle\LegacyIntegrationBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class WebfactoryLegacyIntegrationExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('webfactory_legacy_integration.legacy_application_bootstrap_file', $config['legacyApplicationBootstrapFile']);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        switch (@$config['parsingMode']) {
            case 'html5':
                $container->setParameter('webfactory_legacy_integration.parser_class', Webfactory\Dom\PolyglotHTML5ParsingHelper::class);
                break;
            case 'xhtml10':
                $container->setParameter('webfactory_legacy_integration.parser_class', Webfactory\Dom\XHTML10ParsingHelper::class);
                break;
        }
    }
}
