<?php
/*
 * (c) webfactory GmbH <info@webfactory.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Webfactory\Bundle\LegacyIntegrationBundle\Integration;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Webfactory\Dom\BaseParsingHelper;
use Webfactory\Dom\Exception\ParsingException;

class XPathHelperFactory
{
    /** @var BaseParsingHelper */
    protected $parser;

    /** @var LegacyApplication */
    protected $legacyApplication;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(BaseParsingHelper $parser, LegacyApplication $legacy, LoggerInterface $logger = null)
    {
        $this->parser = $parser;
        $this->legacyApplication = $legacy;
        $this->logger = $logger ?: new NullLogger();
    }

    public function createHelper()
    {
        $content = $this->legacyApplication->getResponse()->getContent();

        try {
            return new XPathHelper($this->parser, $content);
        } catch (ParsingException $exception) {
            if (\function_exists('tidy_repair_string')) {
                $this->logger->notice('Failed parsing the legacy response as XHTML, trying to clean up with Tidy.', ['exception' => $exception, 'legacy_response' => $exception->getXmlInput()]);

                return new XPathHelper($this->parser, tidy_repair_string($content, ['output-xhtml' => true, 'wrap' => 0], 'utf8'));
            } else {
                $this->logger->warning('Failed to process legacy response as XHTML.', ['exception' => $exception, 'legacy_response' => $exception->getXmlInput()]);

                throw $exception;
            }
        }
    }
}
