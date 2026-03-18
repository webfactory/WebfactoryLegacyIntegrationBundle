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
use Webfactory\Bundle\LegacyIntegrationBundle\Integration\Filter\IgnoreHeader as IgnoreHeaderFilter;

#[Attribute(Attribute::TARGET_METHOD)]
class IgnoreHeader implements Factory
{
    protected $header;

    public function __construct(string $header)
    {
        $this->header = $header;
    }

    public function createFilter(ContainerInterface $container)
    {
        return new IgnoreHeaderFilter($this->header);
    }
}
