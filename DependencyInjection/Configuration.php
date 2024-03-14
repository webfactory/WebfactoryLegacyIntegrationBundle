<?php
/*
 * (c) webfactory GmbH <info@webfactory.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Webfactory\Bundle\LegacyIntegrationBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('webfactory_legacy_integration');

        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->scalarNode('legacyApplicationBootstrapFile')->defaultValue('%project.webdir%/wfD2Engine.php')->end()
                ->scalarNode('parsingMode')
                    ->isRequired()
                    ->validate()
                        ->ifNotInArray(['html5', 'xhtml10'])
                        ->thenInvalid('Invalid parsing mode (choose html5 or xhtm10)')
                ->end();

        return $treeBuilder;
    }
}
