<?php

namespace Webfactory\Bundle\LegacyIntegrationBundle\Integration;

use Symfony\Component\HttpFoundation\Response;
use Webfactory\Dom\PolyglotHTML5Parser;

class FragmentalResponse extends Response {

    protected $document;
    protected $parser;

    public function __construct($parser, $content = '', $status = 200, $headers = array()) {
        $this->parser = $parser;
        parent::__construct($content, $status, $headers);
    }

    public function getFragment($expression) {
        $html = '';

        if ($document = $this->getDocument()) {
            $xpath = new \DOMXPath($document);
            $xpath->registerNamespace('html', 'http://www.w3.org/1999/xhtml');
            // TODO: Automatisch alle Namespaces registrieren...
            foreach ($xpath->query($expression) as $node) {
                $html .= $this->parser->dumpElement($node);
            }
        }

        return $html;
    }

    protected function getDocument() {
        if (!$this->document) {
            $this->document = $this->parser->parseDocument($this->getContent());
        }
        return $this->document;
    }

}