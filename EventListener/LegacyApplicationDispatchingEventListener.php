<?php
/*
 * (c) webfactory GmbH <info@webfactory.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Webfactory\Bundle\LegacyIntegrationBundle\EventListener;

use Doctrine\Common\Annotations\Reader;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Webfactory\Bundle\LegacyIntegrationBundle\Integration\Annotation\Dispatch;
use Webfactory\Bundle\LegacyIntegrationBundle\Integration\Filter;

class LegacyApplicationDispatchingEventListener
{

    protected $container;
    protected $reader;
    protected $stopwatch;
    protected $filters = array();

    public function __construct(ContainerInterface $container, Reader $reader)
    {
        $this->container = $container;
        $this->reader = $reader;
    }

    public function addFilter(Filter $filter)
    {
        $this->filters[] = $filter;
    }

    public function onKernelController(FilterControllerEvent $event)
    {
        if ($event->getRequestType() != HttpKernelInterface::MASTER_REQUEST) {
            return;
        }

        if (!is_array($controller = $event->getController())) {
            return;
        }

        $object = new \ReflectionObject($controller[0]);
        $method = $object->getMethod($controller[1]);
        $origRequest = $event->getRequest();
        $request = $origRequest->duplicate();

        $dispatch = false;
        foreach ($this->reader->getMethodAnnotations($method) as $configuration) {
            if ($configuration instanceof Dispatch) {
                $dispatch = true;
                $dispatchConfig = $configuration->getServer();
                if(empty($dispatchConfig)) {
                    break;
                }
                $dispatchServer = array_key_exists('server' , $dispatchConfig) ? $dispatchConfig['server'] : null;
                $request = $request->duplicate();
                $server = $_SERVER;
                foreach($dispatchServer as $k=>$v) {
                    $server[$k] = $v;
                }
                $request->server->replace($server);
                break;
            }
        }

        if ($dispatch) {

            $response = $this->getLegacyApplication()->handle($request, $event->getRequestType(), false);

            foreach ($this->filters as $filter) {
                $filter->filter($event, $response);
                if ($event->isPropagationStopped()) {
                    break;
                }
            }
        }
    }

    protected function getLegacyApplication()
    {
        return $this->container->get('webfactory_legacy_integration.legacy_application');
    }
}

