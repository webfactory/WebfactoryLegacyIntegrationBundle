<?php
/*
 * (c) webfactory GmbH <info@webfactory.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Webfactory\Bundle\LegacyIntegrationBundle\Integration\Filter;

use Doctrine\Common\Annotations\Reader;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Webfactory\Bundle\LegacyIntegrationBundle\Integration\Filter as FilterInterface;

class ControllerAnnotations implements FilterInterface
{
    protected $reader;
    protected $container;

    public function __construct(Reader $reader, ContainerInterface $container)
    {
        $this->reader = $reader;
        $this->container = $container;
    }

    public function filter(ControllerEvent $event, Response $response)
    {
        if (!\is_array($controller = $event->getController())) {
            return;
        }

        $object = new \ReflectionObject($controller[0]);
        $method = $object->getMethod($controller[1]);

        foreach ($this->reader->getMethodAnnotations($method) as $annotation) {
            if ($annotation instanceof Factory) {
                $annotation->createFilter($this->container)->filter($event, $response);
                if ($event->isPropagationStopped()) {
                    break;
                }
            }
        }
    }
}
