<?php
/*
 * (c) webfactory GmbH <info@webfactory.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Webfactory\Bundle\LegacyIntegrationBundle\Twig;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;
use Twig\TwigFunction;

class Extension extends AbstractExtension implements GlobalsInterface
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
        return [
            new TwigFunction('webfactory_legacy_integration_embed', [$this, 'embedString']),
            new TwigFunction('webfactory_legacy_integration_embed_result', [$this, 'getEmbedResult'], ['is_safe' => ['html']]),
        ];
    }

    public function getGlobals()
    {
        return [
            'legacyApplication' => $this,
        ];
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
     *
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
        if (null === $this->embedResult) {
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
