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
class KeepCookies
{
    public $value;

    public function shouldKeep($name) {
        return $this->value === null || in_array($name, $this->value);
    }

}
