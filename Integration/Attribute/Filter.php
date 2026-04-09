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
use Webfactory\Bundle\LegacyIntegrationBundle\Integration\Filter as FilterInterface;
use Webfactory\Bundle\LegacyIntegrationBundle\Integration\Filter\Factory;

#[Attribute(Attribute::TARGET_METHOD)]
class Filter implements Factory
{
    protected $class;
    protected $service;

    public function __construct(?string $class = null, ?string $service = null)
    {
        $this->class = $class;
        $this->service = $service;

        if (!$this->class && !$this->service) {
            throw new \Exception('Parameter "class" or "service" is missing in '.self::class.'.');
        }
    }

    public function createFilter(ContainerInterface $container)
    {
        if ($class = $this->class) {
            if (!class_exists($class)) {
                throw new \Exception('Unknown class '.$class.' configured with the '.self::class.' attribute.');
            }
            $filter = new $class();
        }

        if ($service = $this->service) {
            if (!$container->has($service)) {
                throw new \Exception('Unknown service '.$service.' configured with the '.self::class.' attribute.');
            }
            $filter = $container->get($service);
        }

        if (!$filter instanceof FilterInterface) {
            throw new \Exception('Class '.\get_class($filter).' configured with the '.self::class.' attribute is not a '.FilterInterface::class.'.');
        }

        return $filter;
    }
}
