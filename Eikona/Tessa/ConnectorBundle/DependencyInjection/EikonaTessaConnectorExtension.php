<?php

    namespace Eikona\Tessa\ConnectorBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class EikonaTessaConnectorExtension extends Extension implements PrependExtensionInterface
{

    public function prepend(ContainerBuilder $container)
    {
        $loader = new YamlFileLoader(
                $container,
                new FileLocator(__DIR__.'/../Resources/config')
        );
        //        $loader->load('parameters.yml');
        $loader->load('config.yml');
        $configs = $container->getExtensionConfig($this->getAlias());
        $config = $this->processConfiguration(new Configuration(), $configs);
        $container->prependExtensionConfig($this->getAlias(), $config);
    }

    public function getAlias()
    {
        return 'pim_eikona_tessa_connector';
    }

    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        //        $configuration = new Configuration();
        //        $config = $this->processConfiguration($configuration, $configs);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
        $loader->load('event_listener.yml');
        $loader->load('converters.yml');
        $loader->load('formatters.yml');
        $loader->load('attribute_types.yml');
        $loader->load('comparators.yml');
        $loader->load('updaters.yml');
        $loader->load('factories.yml');
        $loader->load('validators.yml');
        $loader->load('normalizers.yml');

        $loader->load('query_builders.yml');

        $loader->load('datagrid/attribute_types.yml');
        $loader->load('datagrid/filters.yml');

    }
}
