<?php
/*
 * (c) webfactory GmbH <info@webfactory.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Webfactory\Bundle\LegacyIntegrationBundle\Integration\Attribute;

use Attribute;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Webfactory\Bundle\LegacyIntegrationBundle\Integration\Filter\Factory;
use Webfactory\Bundle\LegacyIntegrationBundle\Integration\Filter\IgnoreRedirect as IgnoreRedirectFilter;

#[Attribute(Attribute::TARGET_METHOD)]
class IgnoreRedirect implements Factory
{
    public function createFilter(ContainerInterface $container)
    {
        return new IgnoreRedirectFilter();
    }
}
