<?php
/*
 * (c) webfactory GmbH <info@webfactory.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Webfactory\Bundle\LegacyIntegrationBundle\Integration\Annotation;

use Webfactory\Bundle\LegacyIntegrationBundle\Integration\Attribute\Filter as FilterAttribute;

/**
 * @Annotation
 * @deprecated Use the attribute instead.
 */
class Filter extends FilterAttribute
{
    public function __construct($values)
    {
        trigger_deprecation(
            'webfactory/legacy-integration-bundle',
            '2.4.0',
            'The %s annotation has been deprecated, use the %s attribute instead.',
            __CLASS__,
            FilterAttribute::class
        );

        parent::__construct($values['class'] ?? null, $values['service'] ?? null);
    }
}
