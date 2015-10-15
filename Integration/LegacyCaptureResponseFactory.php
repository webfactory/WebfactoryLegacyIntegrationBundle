<?php
/*
 * (c) webfactory GmbH <info@webfactory.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Webfactory\Bundle\LegacyIntegrationBundle\Integration;

use Symfony\Component\HttpFoundation\RedirectResponse;

use Symfony\Component\HttpFoundation\Response;

class LegacyCaptureResponseFactory
{

    public static function create($legacyExecutionCallback)
    {
        ob_start();

        $statusCode = call_user_func($legacyExecutionCallback) ? : 200;

        if (function_exists('http_response_code')) {
            $statusCode = http_response_code();
        }

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
