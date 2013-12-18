<?php
/*
 * (c) webfactory GmbH <info@webfactory.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Webfactory\Bundle\LegacyIntegrationBundle\Integration;

use Webfactory\Dom\BaseParsingHelper;

class XPathHelper
{

    protected $parser;
    protected $document;

    public function __construct(BaseParsingHelper $parser, $content)
    {
        $this->parser = $parser;
        $this->document = $parser->parseDocument($content);
    }

    public function getFragment($expression)
    {
        $xpath = $this->parser->createXPath($this->document);
        return $this->parser->dump($xpath->query($expression));
    }
}
