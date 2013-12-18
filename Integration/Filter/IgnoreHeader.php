<?php
/*
 * (c) webfactory GmbH <info@webfactory.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Webfactory\Bundle\LegacyIntegrationBundle\Integration\Filter;

use Symfony\Component\HttpFoundation\Response;

class IgnoreHeader extends PassthruLegacyResponseFilter
{

    protected $header;

    public function __construct($header)
    {
        $this->header = $header;
    }

    protected function check(Response $response)
    {
        return $response->headers->has($this->header);
    }
}
