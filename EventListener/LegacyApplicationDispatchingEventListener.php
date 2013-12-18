<?php

namespace Webfactory\Bundle\LegacyIntegrationBundle\EventListener;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Debug\Stopwatch;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Doctrine\Common\Annotations\Reader;
use Webfactory\Bundle\LegacyIntegrationBundle\Integration\Annotation\Dispatch;
use Webfactory\Bundle\LegacyIntegrationBundle\Integration\Filter;

class LegacyApplicationDispatchingEventListener {

    protected $container;
    protected $reader;
    protected $stopwatch;
    protected $filters = array();

    public function __construct(ContainerInterface $container, Reader $reader, Stopwatch $stopwatch = null) {
        $this->container = $container;
        $this->reader = $reader;
        $this->stopwatch = $stopwatch;
    }

    public function addFilter(Filter $filter) {
        $this->filters[] = $filter;
    }

    public function onKernelController(FilterControllerEvent $event) {
        if ($event->getRequestType() != HttpKernelInterface::MASTER_REQUEST) {
            return;
        }

        if (!is_array($controller = $event->getController())) {
            return;
        }

        if ($this->stopwatch) {
            $e = $this->stopwatch->start('Parsing');
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

        if ($this->stopwatch) {
            $e->stop();
        }

        if ($dispatch) {

            if ($this->stopwatch) {
                $e = $this->stopwatch->start('Dispatching');
            }

            $response = $this->getLegacyApplication()->handle($event->getRequest(), $event->getRequestType(), false);

            if ($this->stopwatch) {
                $e->stop();
                $e = $this->stopwatch->start('Filters');
            }

            foreach ($this->filters as $filter) {
                $filter->filter($event, $response);
                if ($event->isPropagationStopped()) {
                    break;
                }
            }

            if ($this->stopwatch) {
                $e->stop();
            }
        }
    }

    protected function getLegacyApplication() {
        return $this->container->get('webfactory_legacy_integration.legacy_application');
    }

}

