<?php

    namespace Eikona\Tessa\ReferenceDataBundle\DependencyInjection;

    use Symfony\Component\Config\FileLocator;
    use Symfony\Component\DependencyInjection\ContainerBuilder;
    use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
    use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
    use Symfony\Component\HttpKernel\DependencyInjection\Extension;

    /**
     * This is the class that loads and manages your bundle configuration
     *
     * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
     */
    class EikonaTessaReferenceDataExtension extends Extension implements PrependExtensionInterface
    {
        /**
         * {@inheritdoc}
         */
        public function load(array $configs, ContainerBuilder $container)
        {
            $loader = new YamlFileLoader(
                    $container, new FileLocator(__DIR__.'/../Resources/config')
            );
            $loader->load('form_types.yml');

        }

        //    public function getAlias()
        //    {
        //        return 'pim_eikona_tessa_reference_data';
        //    }
        /**
         * Allow an extension to prepend the extension configurations.
         *
         * @param ContainerBuilder $container
         */
        public function prepend(ContainerBuilder $container)
        {
            $container->prependExtensionConfig(
                    'twig',
                    ['form_themes' => ['EikonaTessaReferenceDataBundle:form:fields.html.twig']]
            );
        }
    }
