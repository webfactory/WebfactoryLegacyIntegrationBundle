<?php
/*
 * (c) webfactory GmbH <info@webfactory.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Webfactory\Bundle\LegacyIntegrationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Webfactory\Bundle\LegacyIntegrationBundle\Integration\Annotation as Legacy;

class PassthruController extends Controller
{
    /**
     * @Legacy\Dispatch
     * @Legacy\Passthru
     */
    public function indexAction()
    {
    }
}
