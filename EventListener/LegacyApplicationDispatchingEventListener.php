<?php
/*
 * (c) webfactory GmbH <info@webfactory.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Webfactory\Bundle\LegacyIntegrationBundle\EventListener;

use Doctrine\Common\Annotations\Reader;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Webfactory\Bundle\LegacyIntegrationBundle\Integration\Annotation\Dispatch;
use Webfactory\Bundle\LegacyIntegrationBundle\Integration\Filter;
use Webfactory\Bundle\LegacyIntegrationBundle\Integration\LegacyApplication;

class LegacyApplicationDispatchingEventListener
{
    /**
     * @var LegacyApplication
     */
    private $legacyApplication;

    protected $reader;

    protected $stopwatch;

    /**
     * @var Filter[]
     */
    protected $filters = [];

    public function __construct(LegacyApplication $legacyApplication, Reader $reader)
    {
        $this->legacyApplication = $legacyApplication;
        $this->reader = $reader;
    }

    public function addFilter(Filter $filter)
    {
        $this->filters[] = $filter;
    }

    public function onKernelController(FilterControllerEvent $event)
    {
        if (!\is_array($controller = $event->getController())) {
            return;
        }

        $object = new \ReflectionObject($controller[0]);
        $method = $object->getMethod($controller[1]);

        $dispatch = false;
        foreach ($this->reader->getMethodAnnotations($method) as $configuration) {
            if ($configuration instanceof Dispatch) {
                $dispatch = true;
                break;
            }
        }

        if ($dispatch) {
            $response = $this->legacyApplication->handle($event->getRequest(), $event->getRequestType(), false);

            foreach ($this->filters as $filter) {
                $filter->filter($event, $response);
                if ($event->isPropagationStopped()) {
                    break;
                }
            }
        }
    }
}
