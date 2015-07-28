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
    private $path = null;

    public function __construct($path = null)
    {
        if($path !== null) {
            $this->path = $path;
        }
    }

    public function getPath()
    {
        return $this->path;
    }
}
