<?php

namespace Webfactory\Bundle\LegacyIntegrationBundle\Integration;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\DomCrawler\Crawler;

class FragmentalResponse extends Response {

    protected $crawler;

    public function getFragment($expression) {
        $html = '';
        if ($crawler = $this->getCrawler()) {
            foreach ($crawler->filter($expression) as $node) {
                $html .= $node->ownerDocument->saveXML($node);
            }
        }

        return $html;
    }

    protected function getCrawler() {
        if (!$this->crawler) {
            $this->prepare();
            $this->crawler = new Crawler();
            $this->crawler->addContent($this->getContent(), $this->headers->get('Content-Type'));
        }

        return $this->crawler;
    }

}