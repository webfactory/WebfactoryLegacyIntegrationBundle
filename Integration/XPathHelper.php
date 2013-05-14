<?php

namespace Webfactory\Bundle\LegacyIntegrationBundle\Integration;

use Webfactory\Dom\BaseParsingHelper;

class XPathHelper {

    protected $parser;
    protected $document;

    public function __construct(BaseParsingHelper $parser, $content) {
        $this->parser = $parser;
        $this->document = $parser->parseDocument($content);
    }

    public function getFragment($expression) {
        $xpath = $this->parser->createXPath($this->document);
        return $this->parser->dump($xpath->query($expression));
    }

}
