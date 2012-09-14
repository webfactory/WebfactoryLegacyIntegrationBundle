<?php

namespace Webfactory\Bundle\LegacyIntegrationBundle\Integration;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Webfactory\Dom\BaseParsingHelper;

class LegacyApplication extends IntegratableApplication {

    protected $bootstrapFile;
    protected $dispatched = false;
    protected $response;

    public function __construct($bootstrapFile, BaseParsingHelper $parser) {
        $this->bootstrapFile = $bootstrapFile;
        parent::__construct($parser);
    }

    public function isDispatched() {
        return $this->dispatched;
    }

    public function dispatch() {
        if (!$this->dispatched) {
            $this->dispatched = true;
            $legacyBootstrap = $this->bootstrapFile;

            ob_start();
            include($legacyBootstrap);

            if (headers_sent())
                throw new \Exception("Du musst sicherstellen, dass die Legacy-Anwendung $legacyBootstrap keine Header bzw. Output sendet (output_buffering)!");

            /**
             * http_response_code geht leider erst mit PHP5.4.
             * Wir wollen aber eigentlich sowieso, dass alle Responses <> 200 mit Exceptions realisiert werden.
             * Die Exception Page wird über den Kernel gelöst
             */
            $statusCode = 200;

            $content = ob_get_contents();
            ob_end_clean();

            $headers = headers_list();
            $responseHeaders = array();
            foreach ($headers as $header) {
                $header = preg_match('(^([^:]+):(.*)$)', $header, $matches);
                $headerName = trim($matches[1]);
                $headerValue = trim($matches[2]);
                $responseHeaders[$headerName][] = $headerValue;
            }
            header_remove();
            
            if (isset($responseHeaders['Location'])) {
                $statusCode = '302';
                unset($responseHeaders['Expires']);
            } 
            
            $this->response = new Response($content, $statusCode, $responseHeaders);                
        }
    }

    public function getResponse() {
        if ($this->response)
            return $this->response;

        throw new \Exception("Die Altanweung hat noch keine Response generiert. Eventuell fehlt die Annotation /** @IntegrateLegacyApplication */ an der aktuellen Controller-Action?");
    }

}