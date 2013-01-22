<?php

namespace Webfactory\Bundle\LegacyIntegrationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Webfactory\Bundle\LegacyIntegrationBundle\Integration\Annotation\Dispatch;
use Webfactory\Bundle\LegacyIntegrationBundle\Integration\Annotation\Passthru;

class PassthruController extends Controller {

    /**
     * @Dispatch
     * @Passthru
     */
    public function indexAction() {

    }

}