<?php

namespace Webfactory\Bundle\LegacyIntegrationBundle\Integration;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\HttpKernel\HttpKernelInterface;

class LegacyCaptureResponseFactory {

    public static function create($legacyExecutionCallback) {
        ob_start();
        $statusCode = call_user_func($legacyExecutionCallback) ? : 200;

        if (headers_sent()) {
            throw new LegacyIntegrationException("It must be possible to caputure the legacy application's output with ob_start(). Headers and/or output must not have been sent to the client.");
        }

        $content = ob_get_contents();
        ob_end_clean();

        $headers = headers_list();
        header_remove();

        $responseHeaders = array();

        foreach ($headers as $header) {
            $header = preg_match('(^([^:]+):(.*)$)', $header, $matches);
            $headerName = trim($matches[1]);
            $headerValue = trim($matches[2]);
            $responseHeaders[$headerName][] = $headerValue;
        }

        if (isset($responseHeaders['Location'])) {
            unset($responseHeaders['Expires']);
            return new RedirectResponse($responseHeaders['Location'][0], 302, $responseHeaders);
        }

        return new Response($content, $statusCode, $responseHeaders);
    }

}
