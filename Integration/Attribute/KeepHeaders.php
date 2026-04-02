<?php
/*
 * (c) webfactory GmbH <info@webfactory.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Webfactory\Bundle\LegacyIntegrationBundle\Integration\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class KeepHeaders
{
    public $value;

    public function __construct(?array $value = null)
    {
        $this->value = $value;
    }

    public function shouldKeep($name)
    {
        return null === $this->value || \in_array($name, $this->value);
    }
}
