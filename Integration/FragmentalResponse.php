<?php

namespace Webfactory\Bundle\LegacyIntegrationBundle\Integration;

use Symfony\Component\HttpFoundation\Response;
use Webfactory\Dom\PolyglotHTML5Parser;

class FragmentalResponse extends Response {

    protected $document;

    public function getFragment($expression) {
        $html = '';

        $parser = new PolyglotHTML5Parser();
        if ($document = $this->getDocument()) {
            $xpath = new \DOMXPath($document);
            $xpath->registerNamespace('html', 'http://www.w3.org/1999/xhtml');
            // TODO: Automatisch alle Namespaces registrieren...
            foreach ($xpath->query($expression) as $node) {
                $html .= $parser->dumpElement($node);
            }
        }

        return $html;
    }

    protected function getDocument() {
        if (!$this->document) {
            $parser = new PolyglotHTML5Parser();
            $this->document = $parser->parseDocument($this->getContent());
        }
        return $this->document;
    }

}