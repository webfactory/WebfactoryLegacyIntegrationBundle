<?php
/*
 * (c) webfactory GmbH <info@webfactory.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Webfactory\Bundle\LegacyIntegrationBundle\Integration\Filter;

use Doctrine\Common\Annotations\Reader;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Webfactory\Bundle\LegacyIntegrationBundle\Integration\Annotation\KeepCookies;
use Webfactory\Bundle\LegacyIntegrationBundle\Integration\Annotation\KeepHeaders;
use Webfactory\Bundle\LegacyIntegrationBundle\Integration\Filter as FilterInterface;

class KeepCookiesAndHeadersFilter implements FilterInterface
{
    /** @var Reader */
    private $reader;

    /** @var Response */
    private $legacyResponse;

    /** @var KeepHeaders */
    private $keepHeadersAnnotation;

    /** @var KeepCookies */
    private $keepCookiesAnnotation;

    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    public function filter(FilterControllerEvent $event, Response $response)
    {
        if (!\is_array($controller = $event->getController())) {
            return;
        }

        $this->legacyResponse = $response;

        $object = new \ReflectionObject($controller[0]);
        $method = $object->getMethod($controller[1]);

        foreach ($this->reader->getMethodAnnotations($method) as $annotation) {
            if ($annotation instanceof KeepHeaders) {
                $this->keepHeadersAnnotation = $annotation;
            } elseif ($annotation instanceof KeepCookies) {
                $this->keepCookiesAnnotation = $annotation;
            }
        }
    }

    public function onKernelResponse(FilterResponseEvent $event)
    {
        if (!$this->legacyResponse) {
            return;
        }

        $response = $event->getResponse();
        $legacyHeaders = $this->legacyResponse->headers;

        if ($this->keepHeadersAnnotation) {
            foreach ($legacyHeaders->all() as $name => $values) {
                if ($this->keepHeadersAnnotation->shouldKeep($name)) {
                    foreach ($values as $value) {
                        $response->headers->set($name, $value);
                    }
                }
            }
        }

        if ($this->keepCookiesAnnotation) {
            foreach ($legacyHeaders->getCookies() as $cookie) {
                /** @var Cookie $cookie */
                if ($this->keepCookiesAnnotation->shouldKeep($cookie->getName())) {
                    $response->headers->setCookie($cookie);
                }
            }
        }
    }
}
