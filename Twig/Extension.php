<?php
/*
 * (c) webfactory GmbH <info@webfactory.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Webfactory\Bundle\LegacyIntegrationBundle\Twig;

use Psr\Container\ContainerInterface;
use Symfony\Contracts\Service\ServiceSubscriberInterface;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;
use Twig\TwigFunction;
use Webfactory\Bundle\LegacyIntegrationBundle\Integration\LegacyApplication;
use Webfactory\Bundle\LegacyIntegrationBundle\Integration\XPathHelper;

class Extension extends AbstractExtension implements GlobalsInterface, ServiceSubscriberInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var string
     */
    private $embedResult = null;

    public static function getSubscribedServices(): array
    {
        return [
            LegacyApplication::class,
            XPathHelper::class,
        ];
    }

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('webfactory_legacy_integration_embed', [$this, 'embedString']),
            new TwigFunction('webfactory_legacy_integration_embed_result', [$this, 'getEmbedResult'], ['is_safe' => ['html']]),
        ];
    }

    public function getGlobals(): array
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
     */
    public function xpath($xpath): string
    {
        return $this->getXPathHelper()->getFragment($xpath);
    }

    private function getXPathHelper()
    {
        return $this->container->get(XPathHelper::class);
    }

    public function embedString($needle, $content)
    {
        if (null === $this->embedResult) {
            $legacyApp = $this->container->get(LegacyApplication::class);
            $this->embedResult = $legacyApp->getResponse()->getContent();
        }

        $this->embedResult = str_replace($needle, $content, $this->embedResult);
    }

    public function getEmbedResult()
    {
        return $this->embedResult;
    }
}
