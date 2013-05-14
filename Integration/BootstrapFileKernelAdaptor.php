<?php

namespace Webfactory\Bundle\LegacyIntegrationBundle\Integration;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class BootstrapFileKernelAdaptor implements HttpKernelInterface {

    protected $file;

    public function __construct($filename) {
        $this->file = $filename;
    }

    public function handle(Request $request, $type = HttpKernelInterface::MASTER_REQUEST, $catch = true) {
        $file = $this->file;
        return LegacyCaptureResponseFactory::create(function() use ($file, $request) {
            include($file);
        });
    }

}
