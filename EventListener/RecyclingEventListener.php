<?php

namespace Webfactory\Bundle\LegacyIntegrationBundle\EventListener;

use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Doctrine\Common\Annotations\Reader;
use Webfactory\Bundle\LegacyIntegrationBundle\Integration\SymfonyApplication;
use Webfactory\Bundle\LegacyIntegrationBundle\Integration\LegacyApplication;
use Webfactory\Bundle\LegacyIntegrationBundle\Integration\Annotation\IntegrateLegacyApplication;

class RecyclingEventListener {

    protected $legacyApplication;
    protected $reader;

    public function __construct(LegacyApplication $legacyApplication, Reader $reader) {
        $this->legacyApplication = $legacyApplication;
        $this->reader = $reader;
    }

    public function onKernelController(FilterControllerEvent $event) {
        if ($event->getRequestType() != HttpKernelInterface::MASTER_REQUEST)
            return;

        if (!is_array($controller = $event->getController())) {
            return;
        }

        $object = new \ReflectionObject($controller[0]);
        $method = $object->getMethod($controller[1]);

        $integrate = false;
        foreach ($this->reader->getMethodAnnotations($method) as $configuration) {
            if ($configuration instanceof IntegrateLegacyApplication) {
                $integrate = true;
                break;
            }
        }

        if ($integrate)
            $this->legacyApplication->dispatch();
    }

}

