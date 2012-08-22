<?php

namespace Webfactory\Bundle\LegacyIntegrationBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $treeBuilder->root('webfactory_legacy_integration')
            ->children()
                ->scalarNode('legacyApplicationBootstrapFile')->defaultValue('%project.webdir%/wfD2Engine.php')->end()
                ->scalarNode('strategy')->defaultValue('recycling')->end()
                ->scalarNode('mode')->defaultValue('xhtml10')->end();

        return $treeBuilder;
    }
}
