<?php

namespace Webfactory\Bundle\LegacyIntegrationBundle\EventListener;

use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Doctrine\Common\Annotations\Reader;
use Webfactory\Bundle\LegacyIntegrationBundle\Integration\SymfonyApplication;
use Webfactory\Bundle\LegacyIntegrationBundle\Integration\LegacyApplication;
use Webfactory\Bundle\LegacyIntegrationBundle\Integration\Annotation\Dispatch;
use Webfactory\Bundle\LegacyIntegrationBundle\Integration\Filter;

class KernelEventListener {

    protected $legacyApplication;
    protected $reader;
    protected $filters = array();

    public function __construct(LegacyApplication $legacyApplication, Reader $reader) {
        $this->legacyApplication = $legacyApplication;
        $this->reader = $reader;
    }

    public function addIntegrationFilter(Filter $filter) {
        $this->filters[] = $filter;
    }

    public function onKernelController(FilterControllerEvent $event) {
        if ($event->getRequestType() != HttpKernelInterface::MASTER_REQUEST)
            return;

        if (!is_array($controller = $event->getController())) {
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
            $this->legacyApplication->dispatch();
            $response = $this->legacyApplication->getResponse();
            foreach ($this->filters as $filter) {
                $filter->filter($event, $response);
                if ($event->isPropagationStopped())
                    break;
            }
        }
    }

}

