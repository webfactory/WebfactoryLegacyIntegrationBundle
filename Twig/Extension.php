<?php
/*
 * (c) webfactory GmbH <info@webfactory.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Webfactory\Bundle\LegacyIntegrationBundle\Twig;

use Symfony\Component\DependencyInjection\ContainerInterface;

class Extension extends \Twig_Extension
{

    protected $legacyApplication;
    protected $container;

    protected $embedResult = null;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('webfactory_legacy_integration_embed', array($this, 'embedString')),
            new \Twig_SimpleFunction('webfactory_legacy_integration_embed_result', array($this, 'getEmbedResult'), array('is_safe' => array('html')))
        );
    }

    public function getGlobals()
    {
        return array(
            'legacyApplication' => $this
        );
    }

    public function getName()
    {
        return 'webfactory_legacy_integration';
    }

    /** @deprecated */
    public function getFragmentalResponse()
    {
        return $this->getXPathHelper();
    }

    /**
     * Evaluate $xpath search query on the legacy content and get a string representation of matching elements.
     *
     * @param string $xpath
     * @return string
     */
    public function xpath($xpath)
    {
        return $this->getXPathHelper()->getFragment($xpath);
    }

    protected function getXPathHelper()
    {
        return $this->container->get('webfactory_legacy_integration.xpath_helper');
    }

    public function embedString($needle, $content)
    {
        if ($this->embedResult === null) {
            $legacyApp = $this->container->get('webfactory_legacy_integration.legacy_application');
            $this->embedResult = $legacyApp->getResponse()->getContent();
        }

        $this->embedResult = str_replace($needle, $content, $this->embedResult);
    }

    public function getEmbedResult()
    {
        return $this->embedResult;
    }
}
