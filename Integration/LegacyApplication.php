<?php
/*
 * (c) webfactory GmbH <info@webfactory.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Webfactory\Bundle\LegacyIntegrationBundle\Integration;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class LegacyApplication implements HttpKernelInterface
{
    /** @var Response */
    protected $response;

    /** @var HttpKernelInterface */
    protected $legacyKernel;

    public function setLegacyKernel(HttpKernelInterface $kernel)
    {
        $this->legacyKernel = $kernel;
    }

    public function handle(Request $request, $type = HttpKernelInterface::MASTER_REQUEST, $catch = true)
    {
        if (null === $this->response) {
            // Dispatch legacy application only once.
            $this->response = $this->legacyKernel->handle($request, $type, $catch);
        }

        return $this->response;
    }

    public function isDispatched()
    {
        return null !== $this->response;
    }

    /** @return Response */
    public function getResponse()
    {
        if (null === $this->response) {
            throw new LegacyIntegrationException('The legacy application has not been started or has not generated a response. Maybe the @Dispatch annotation is missing for the current controller?');
        }

        return $this->response;
    }
}
