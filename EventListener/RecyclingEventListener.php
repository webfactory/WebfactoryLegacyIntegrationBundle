<?php

namespace Webfactory\Bundle\LegacyIntegrationBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Webfactory\Bundle\LegacyIntegrationBundle\Integration\SymfonyApplication;
use Webfactory\Bundle\LegacyIntegrationBundle\Integration\LegacyApplication;

class RecyclingEventListener {

    protected $legacyApplication;

    public function __construct(LegacyApplication $legacyApplication) {
        $this->legacyApplication = $legacyApplication;
    }

    public function onKernelRequest(GetResponseEvent $event) {
        if ($event->getRequestType() != HttpKernelInterface::MASTER_REQUEST)
            return;

        try {

            $this->legacyApplication->getResponse();

        } catch (\Exception $e) {}
    }

}

