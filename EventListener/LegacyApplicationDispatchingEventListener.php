<?php
/*
 * (c) webfactory GmbH <info@webfactory.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Webfactory\Bundle\LegacyIntegrationBundle\EventListener;

use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Webfactory\Bundle\LegacyIntegrationBundle\Integration\Attribute\Dispatch;
use Webfactory\Bundle\LegacyIntegrationBundle\Integration\Filter;
use Webfactory\Bundle\LegacyIntegrationBundle\Integration\LegacyApplication;

class LegacyApplicationDispatchingEventListener
{
    /**
     * @var LegacyApplication
     */
    private $legacyApplication;

    protected $stopwatch;

    /**
     * @var Filter[]
     */
    protected $filters = [];

    public function __construct(LegacyApplication $legacyApplication)
    {
        $this->legacyApplication = $legacyApplication;
    }

    public function addFilter(Filter $filter)
    {
        $this->filters[] = $filter;
    }

    public function onKernelController(ControllerEvent $event)
    {
        if (!\is_array($controller = $event->getController())) {
            return;
        }

        $object = new \ReflectionObject($controller[0]);
        $method = $object->getMethod($controller[1]);

        if (!$method->getAttributes(Dispatch::class)) {
            return;
        }

        $response = $this->legacyApplication->handle($event->getRequest(), $event->getRequestType(), false);

        foreach ($this->filters as $filter) {
            $filter->filter($event, $response);
            if ($event->isPropagationStopped()) {
                break;
            }
        }
    }
}
