<?php
/*
 * (c) webfactory GmbH <info@webfactory.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Webfactory\Bundle\LegacyIntegrationBundle\Integration\Annotation;

use Symfony\Component\DependencyInjection\Container;
use Webfactory\Bundle\LegacyIntegrationBundle\Integration\Filter\Factory;
use Webfactory\Bundle\LegacyIntegrationBundle\Integration\Filter\IgnoreHeader as IgnoreHeaderFilter;

/**
 * @Annotation
 */
class IgnoreHeader implements Factory
{
    protected $header;

    public function __construct(array $values)
    {
        $this->header = array_shift($values);
        if (!\is_string($this->header)) {
            throw new \Exception("Please define a header with the Webfactory\Bundle\LegacyIntegrationBundle\Integration\Annotation\IgnoreHeader annotation.");
        }
    }

    public function createFilter(Container $container)
    {
        return new IgnoreHeaderFilter($this->header);
    }
}
