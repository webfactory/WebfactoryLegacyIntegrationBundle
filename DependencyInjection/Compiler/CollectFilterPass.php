<?php
/*
 * (c) webfactory GmbH <info@webfactory.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Webfactory\Bundle\LegacyIntegrationBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class CollectFilterPass implements CompilerPassInterface
{

    public function process(ContainerBuilder $container)
    {
        $kernelEventListenerDefinition = $container->getDefinition('webfactory_legacy_integration.kernel_event_listener');

        foreach ($container->findTaggedServiceIds('webfactory_legacy_integration.filter') as $id => $tags) {
            $kernelEventListenerDefinition->addMethodCall('addFilter', array(
                new Reference($id)
            ));
        }
    }
}
