<?php
/*
 * (c) webfactory GmbH <info@webfactory.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Webfactory\Bundle\LegacyIntegrationBundle\Integration\Filter;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Webfactory\Bundle\LegacyIntegrationBundle\Integration\Filter as FilterInterface;

/**
 * Ein LegacyIntergration-Filter, der die Response der Altanwendung
 * unverändert zurückgibt.
 *
 * Subklassen können die check() Methode überschreiben um die
 * legacy-Response nur unter bestimmten Bedingungen zurückzugeben.
 */
class PassthruLegacyResponseFilter implements FilterInterface
{
    public function filter(ControllerEvent $event, Response $response)
    {
        if ($this->check($response)) {
            $event->setController(function () use ($response) {
                return $response;
            });
            $event->stopPropagation();
        }
    }

    protected function check(Response $response)
    {
        return true;
    }
}
