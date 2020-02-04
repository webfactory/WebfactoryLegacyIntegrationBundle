<?php

namespace Webfactory\Bundle\LegacyIntegrationBundle\Integration;

use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Helper class to create a Symfony Response object from raw (legacy) header
 * and content data
 */
class ResponseCaptureHelper
{
    private static $dateFormats = [
        'D, d M Y H:i:s T',
        'D, d-M-y H:i:s T',
        'D, d-M-Y H:i:s T',
        'D, d-m-y H:i:s T',
        'D, d-m-Y H:i:s T',
        'D M j G:i:s Y',
        'D M d H:i:s Y T',
    ];

    public static function createResponse(string $content, int $status, array $headers): Response
    {
        $responseHeaders = [];
        $cookies = [];

        foreach ($headers as $header) {
            preg_match('(^([^:]+):(.*)$)', $header, $matches);
            $headerName = strtolower(trim($matches[1]));
            $headerValue = trim($matches[2]);

            if ($headerName == 'set-cookie') {
                $cookies[] = self::createCookieFromString($headerValue);
            } else {
                $responseHeaders[$headerName][] = $headerValue;
            }
        }

        if (isset($responseHeaders['location'])) {
            unset($responseHeaders['expires']);
            $response = new RedirectResponse($responseHeaders['location'][0], 302, $responseHeaders);
        } else {
            $response = new Response($content, $status, $responseHeaders);
        }

        foreach ($cookies as $cookie) {
            $response->headers->setCookie($cookie);
        }

        return $response;
    }

    private static function createCookieFromString($cookie, $url = null)
    {
        $parts = explode(';', $cookie);

        if (false === strpos($parts[0], '=')) {
            throw new \InvalidArgumentException(sprintf('The cookie string "%s" is not valid.', $parts[0]));
        }

        [$name, $value] = explode('=', array_shift($parts), 2);

        $values = [
            'name'     => trim($name),
            // Cookie value must be decoded, otherwise it is encoded again when we forward it to the Symfony response.
            // That would lead to problems, for example with session cookies whose id can contain a comma.
            'value'    => trim(urldecode($value)),
            'expires'  => 0,
            'path'     => '/',
            'domain'   => '',
            'secure'   => false,
            'httponly' => false,
        ];

        if (null !== $url) {
            if ((false === $urlParts = parse_url($url)) || !isset($urlParts['host'])) {
                throw new \InvalidArgumentException(sprintf('The URL "%s" is not valid.', $url));
            }

            $values['domain'] = $urlParts['host'];
            $values['path'] = isset($urlParts['path']) ? substr($urlParts['path'], 0, strrpos($urlParts['path'], '/')) : '';
        }

        foreach ($parts as $part) {
            $part = trim($part);

            if ('secure' === strtolower($part)) {
                // Ignore the secure flag if the original URI is not given or is not HTTPS
                if (!$url || !isset($urlParts['scheme']) || 'https' != $urlParts['scheme']) {
                    continue;
                }

                $values['secure'] = true;

                continue;
            }

            if ('httponly' === strtolower($part)) {
                $values['httponly'] = true;

                continue;
            }

            if (2 === count($elements = explode('=', $part, 2))) {
                if ('expires' === strtolower($elements[0])) {
                    $elements[1] = self::parseDate($elements[1]);
                }

                $values[strtolower($elements[0])] = $elements[1];
            }
        }

        return new Cookie(
            $values['name'],
            $values['value'],
            $values['expires'],
            $values['path'],
            $values['domain'],
            $values['secure'],
            $values['httponly']
        );
    }

    private static function parseDate($dateValue)
    {
        // trim single quotes around date if present
        if (($length = strlen($dateValue)) > 1 && "'" === $dateValue[0] && "'" === $dateValue[$length - 1]) {
            $dateValue = substr($dateValue, 1, -1);
        }

        foreach (self::$dateFormats as $dateFormat) {
            if (false !== $date = \DateTime::createFromFormat($dateFormat, $dateValue, new \DateTimeZone('GMT'))) {
                return $date->getTimestamp();
            }
        }

        // attempt a fallback for unusual formatting
        if (false !== $date = date_create($dateValue, new \DateTimeZone('GMT'))) {
            return $date->getTimestamp();
        }

        throw new \InvalidArgumentException(sprintf('Could not parse date "%s".', $dateValue));
    }
}
