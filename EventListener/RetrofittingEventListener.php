<?php

namespace Webfactory\Bundle\LegacyIntegrationBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Webfactory\Bundle\LegacyIntegrationBundle\Integration\SymfonyApplication;
use Webfactory\Bundle\LegacyIntegrationBundle\Integration\LegacyApplication;

class RetrofittingEventListener {

    protected $symfonyApplication;
    protected $legacyApplication;
    protected $legacyApplicationIsDispatched = false;

    public function __construct(SymfonyApplication $symfonyApplication, LegacyApplication $legacyApplication) {
        $this->symfonyApplication = $symfonyApplication;
        $this->legacyApplication = $legacyApplication;
    }

    public function onKernelException(GetResponseForExceptionEvent $event) {
        if (($event->getRequestType() != HttpKernelInterface::MASTER_REQUEST) ||
            (!($event->getException() instanceof NotFoundHttpException)) ||
            ($this->legacyApplicationIsDispatched))
            return;

        try {

            $this->legacyApplicationIsDispatched = true;
            $event->setResponse($this->legacyApplication->getResponse());

        } catch(\Exception $e) {

            $event->setException($e);

        }
    }

    public function onKernelResponse(FilterResponseEvent $event) {
        $response = $event->getResponse();

        if (($event->getRequestType() != HttpKernelInterface::MASTER_REQUEST) ||
            ($this->legacyApplicationIsDispatched) ||
            ($response->isRedirect()) ||
            (stripos($response->headers->get('Content-Type'), 'text/html') === false) ||
            (!$response->isOk()))
            return;

        $this->symfonyApplication->setResponse($response);
        $this->legacyApplicationIsDispatched = true;
        $event->setResponse($this->legacyApplication->getResponse());
    }

}

