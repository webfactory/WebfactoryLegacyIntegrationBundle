<?php
/*
 * (c) webfactory GmbH <info@webfactory.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Webfactory\Bundle\LegacyIntegrationBundle\Integration;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class BootstrapFileKernelAdaptor implements HttpKernelInterface
{
    protected $file;

    public function __construct($filename)
    {
        $this->file = $filename;
    }

    /**
     * @return Response
     */
    public function handle(Request $request, $type = HttpKernelInterface::MASTER_REQUEST, $catch = true)
    {
        $file = $this->file;

        return LegacyCaptureResponseFactory::create(function () use ($file, $request) {
            $status = include $file;

            if (1 != $status) {
                return $status;
            }
        });
    }
}
