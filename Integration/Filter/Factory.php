<?php
namespace Webfactory\Bundle\LegacyIntegrationBundle\Integration\Filter;

use Symfony\Component\DependencyInjection\Container;

interface Factory {

    public function createFilter(Container $container);

}