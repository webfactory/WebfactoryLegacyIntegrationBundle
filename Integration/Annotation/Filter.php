<?php
/*
 * (c) webfactory GmbH <info@webfactory.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Webfactory\Bundle\LegacyIntegrationBundle\Integration\Annotation;

use Symfony\Component\DependencyInjection\Container;
use Webfactory\Bundle\LegacyIntegrationBundle\Integration\Filter as FilterInterface;
use Webfactory\Bundle\LegacyIntegrationBundle\Integration\Filter\Factory;

/**
 * @Annotation
 */
class Filter implements Factory
{
    protected $class;
    protected $service;

    public function __construct($values)
    {
        if (isset($values['class'])) {
            $this->class = $values['class'];
        }
        if (isset($values['service'])) {
            $this->service = $values['service'];
        }
        if (!$this->class && !$this->service) {
            throw new \Exception('Parameter "class" or "service" is missing in Webfactory\Bundle\LegacyIntegrationBundle\Integration\Annotation\Filter.');
        }
    }

    public function createFilter(Container $container)
    {
        if ($class = $this->class) {
            if (!class_exists($class)) {
                throw new \Exception('Unknown class '.$class.' configured with the Webfactory\Bundle\LegacyIntegrationBundle\Integration\Annotation\Filter annotation.');
            }
            $filter = new $class();
        }
        if ($service = $this->service) {
            if (!$container->has($service)) {
                throw new \Exception('Unknown service '.$service.' configured with the Webfactory\Bundle\LegacyIntegrationBundle\Integration\Annotation\Filter annotation.');
            }
            $filter = $container->get($service);
        }
        if (!$filter instanceof FilterInterface) {
            throw new \Exception('Class '.\get_class($filter).' configured with the Webfactory\Bundle\LegacyIntegrationBundle\Integration\Annotation\Filter annotation is not a Webfactory\Bundle\LegacyIntegrationBundle\Integration\Filter.');
        }

        return $filter;
    }
}
