<?php
/*
 * (c) webfactory GmbH <info@webfactory.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Webfactory\Bundle\LegacyIntegrationBundle\Integration;

class LegacyCaptureResponseFactory
{
    public static function create($legacyExecutionCallback)
    {
        // Preserve all headers that have previously been set (pre-Legacy startup)
        $preLegacyHeaders = headers_list();
        header_remove();

        try {
            return static::runLegacyAndCaptureResponse($legacyExecutionCallback);
        } finally {
            // Restore all headers that were previously present (before running legacy)
            foreach ($preLegacyHeaders as $header) {
                header($header);
            }
        }
    }

    private static function runLegacyAndCaptureResponse($legacyExecutionCallback)
    {
        ob_start();
        try {
            $statusCode = \call_user_func($legacyExecutionCallback);
            $content = ob_get_contents();
        } finally {
            ob_end_clean();
        }

        if (null === $statusCode) {
            if (\function_exists('http_response_code')) {
                $statusCode = http_response_code() ?: 200;
            } else {
                $statusCode = 200;
            }
        }

        if (headers_sent()) {
            throw new LegacyIntegrationException("It must be possible to caputure the legacy application's output with ob_start(). Headers and/or output must not have been sent to the client.");
        }

        $headers = headers_list();
        header_remove();

        return ResponseCaptureHelper::createResponse($content, $statusCode, $headers);
    }
}
