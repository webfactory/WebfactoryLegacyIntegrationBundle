<?php

namespace Webfactory\Bundle\KernelIntegrationBundle;

use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DomCrawler\Crawler;

class KernelIntegration {

    protected $kernel = null;
    protected $response = null;
    protected $crawler = null;

    public function __construct(HttpKernelInterface $kernel) {
        $this->kernel = $kernel;
    }

    public function handleNonHtmlContent() {
        $this->initialize();
        if ($this->response && ($this->response->isRedirect() || stripos($this->response->headers->get('Content-Type'), 'text/html') === false)) {
            $this->response->send();
            exit;
        }
    }

    public function getFragment($expression) {
        $this->initialize();
        $html = '';
        if ($this->crawler) {
            foreach ($this->crawler->filter($expression) as $node) {
                $html .= $node->ownerDocument->saveXML($node);
            }
        }
        return $html;
    }

    protected function initialize() {
        if (!$this->response) {
            try {
                $this->response = $this->kernel->handle(Request::createFromGlobals(), HttpKernelInterface::MASTER_REQUEST, false);
                $this->response->prepare();
                if (!$this->response->isRedirect()) {
                    $this->crawler = new Crawler();
                    $this->crawler->addContent($this->response->getContent(), $this->response->headers->get('Content-Type'));
                }
            } catch (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $exception) {}
        }
    }

}
