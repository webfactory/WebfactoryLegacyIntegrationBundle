<?php
/*
 * (c) webfactory GmbH <info@webfactory.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Webfactory\Bundle\LegacyIntegrationBundle\Integration\Filter;

use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Webfactory\Bundle\LegacyIntegrationBundle\Integration\Attribute\KeepCookies as KeepCookiesAttribute;
use Webfactory\Bundle\LegacyIntegrationBundle\Integration\Attribute\KeepHeaders as KeepHeadersAttribute;
use Webfactory\Bundle\LegacyIntegrationBundle\Integration\Filter as FilterInterface;

class KeepCookiesAndHeadersFilter implements FilterInterface
{
    /** @var Response */
    private $legacyResponse;

    /** @var KeepHeadersAttribute */
    private $keepHeaders;

    /** @var KeepCookiesAttribute */
    private $keepCookies;

    public function filter(ControllerEvent $event, Response $response)
    {
        if (!\is_array($controller = $event->getController())) {
            return;
        }

        $this->legacyResponse = $response;

        $object = new \ReflectionObject($controller[0]);
        $method = $object->getMethod($controller[1]);

        foreach ($method->getAttributes(KeepHeadersAttribute::class) as $attribute) {
            $this->keepHeaders = $attribute->newInstance();
        }

        foreach ($method->getAttributes(KeepCookiesAttribute::class) as $attribute) {
            $this->keepCookies = $attribute->newInstance();
        }
    }

    public function onKernelResponse(ResponseEvent $event)
    {
        if (!$this->legacyResponse) {
            return;
        }

        $response = $event->getResponse();
        $legacyHeaders = $this->legacyResponse->headers;

        if ($this->keepHeaders) {
            foreach ($legacyHeaders->all() as $name => $values) {
                if ($this->keepHeaders->shouldKeep($name)) {
                    foreach ($values as $value) {
                        $response->headers->set($name, $value);
                    }
                }
            }
        }

        if ($this->keepCookies) {
            foreach ($legacyHeaders->getCookies() as $cookie) {
                /** @var Cookie $cookie */
                if ($this->keepCookies->shouldKeep($cookie->getName())) {
                    $response->headers->setCookie($cookie);
                }
            }
        }
    }
}
