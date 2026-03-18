<?php
/*
 * (c) webfactory GmbH <info@webfactory.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Webfactory\Bundle\LegacyIntegrationBundle\Integration\Annotation;

use Webfactory\Bundle\LegacyIntegrationBundle\Integration\Attribute\IgnoreHeader as IgnoreHeaderAttribute;

/**
 * @Annotation
 * @deprecated Use the attribute instead.
 */
class IgnoreHeader extends IgnoreHeaderAttribute
{
    public function __construct(array $values)
    {
        trigger_deprecation(
            'webfactory/legacy-integration-bundle',
            '2.4.0',
            'The %s annotation has been deprecated, use the %s attribute instead.',
            __CLASS__,
            IgnoreHeaderAttribute::class
        );

        parent::__construct($values['value']);
    }
}
