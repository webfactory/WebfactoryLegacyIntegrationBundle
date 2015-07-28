<?php
/*
 * (c) webfactory GmbH <info@webfactory.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Webfactory\Bundle\LegacyIntegrationBundle\Integration\Annotation;

/**
 * @Annotation
 */
class Dispatch
{
    private $server = null;

    public function __construct($server = null)
    {
        if($server !== null) {
            $this->server = $server;
        }
    }

    public function getServer()
    {
        return $this->server;
    }
}
