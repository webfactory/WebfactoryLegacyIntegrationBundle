<?php
/*
 * (c) webfactory GmbH <info@webfactory.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Webfactory\Bundle\LegacyIntegrationBundle\Integration;

use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpFoundation\Response;

interface Filter {

    public function filter(FilterControllerEvent $event, Response $response);

}
