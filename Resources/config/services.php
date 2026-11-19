<?php

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Webfactory\Bundle\LegacyIntegrationBundle\EventListener\LegacyApplicationDispatchingEventListener;
use Webfactory\Bundle\LegacyIntegrationBundle\Integration\BootstrapFileKernelAdaptor;
use Webfactory\Bundle\LegacyIntegrationBundle\Integration\Filter\ControllerAttributes;
use Webfactory\Bundle\LegacyIntegrationBundle\Integration\Filter\KeepCookiesAndHeadersFilter;
use Webfactory\Bundle\LegacyIntegrationBundle\Integration\LegacyApplication;
use Webfactory\Bundle\LegacyIntegrationBundle\Integration\XPathHelper;
use Webfactory\Bundle\LegacyIntegrationBundle\Integration\XPathHelperFactory;
use Webfactory\Bundle\LegacyIntegrationBundle\Twig\Extension;

use function Symfony\Component\DependencyInjection\Loader\Configurator\inline_service;
use function Symfony\Component\DependencyInjection\Loader\Configurator\param;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();
    $services->defaults()->autowire()->autoconfigure();

    $services->set(BootstrapFileKernelAdaptor::class)
        ->args([param('webfactory_legacy_integration.legacy_application_bootstrap_file')]);
    $services->alias('webfactory_legacy_integration.legacy_kernel', BootstrapFileKernelAdaptor::class);

    $services->set(LegacyApplication::class)
        ->call('setLegacyKernel', [service('webfactory_legacy_integration.legacy_kernel')]);
    $services->alias('webfactory_legacy_integration.legacy_application', LegacyApplication::class)->public();

    $services->set(LegacyApplicationDispatchingEventListener::class)
        ->tag('kernel.event_listener', ['event' => 'kernel.controller', 'method' => 'onKernelController', 'priority' => -210]);

    $services->set(ControllerAttributes::class)->tag('webfactory_legacy_integration.filter');

    $services->set(KeepCookiesAndHeadersFilter::class)
        ->tag('webfactory_legacy_integration.filter')
        ->tag('kernel.event_listener', ['event' => 'kernel.response']);

    $services->set(Extension::class)
        ->tag('twig.extension')
        ->tag('container.service_subscriber');

    $services->set(XPathHelperFactory::class)
        ->args([
            inline_service('%webfactory_legacy_integration.parser_class%'),
            service(LegacyApplication::class),
            service('logger')->nullOnInvalid(),
        ])
        ->tag('monolog.logger', ['channel' => 'webfactory_legacy_integration']);

    $services->set(XPathHelper::class)->factory([service(XPathHelperFactory::class), 'createHelper']);
    $services->alias('webfactory_legacy_integration.xpath_helper', XPathHelper::class)->public();

    $services->alias(ContainerInterface::class, 'service_container');
};
