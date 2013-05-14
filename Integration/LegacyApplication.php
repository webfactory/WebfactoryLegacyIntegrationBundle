<?php

namespace Webfactory\Bundle\LegacyIntegrationBundle\Integration;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Debug\Stopwatch;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Webfactory\Dom\BaseParsingHelper;

class LegacyApplication implements HttpKernelInterface {

    /** @var Response */
    protected $response;

    /** @var HttpKernelInterface */
    protected $legacyKernel;

    public function setLegacyKernel(HttpKernelInterface $kernel) {
        $this->legacyKernel = $kernel;
    }

    public function handle(Request $request, $type = HttpKernelInterface::MASTER_REQUEST, $catch = true) {
        return $this->response = $this->legacyKernel->handle($request, $type, $catch);
    }

    public function isDispatched() {
        return null !== $this->response;
    }

    /** @return Response */
    public function getResponse() {
        if (null === $this->response) {
            throw new LegacyIntegrationException("The legacy application has not been started or has not generated a response. Maybe the @Dispatch annotation is missing for the current controller?");
        }

        return $this->response;
    }

}
