<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUiBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Yaml\Yaml;

class EzPlatformAdminUiExtension extends Extension implements PrependExtensionInterface
{
    /**
     * Loads a specific configuration.
     *
     * @param array $configs An array of configuration values
     * @param ContainerBuilder $container A ContainerBuilder instance
     *
     * @throws \InvalidArgumentException When provided tag is not defined in this extension
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config')
        );

        $loader->load('default_parameters.yml');
        $loader->load('services.yml');
        $loader->load('role.yml');
    }

    /**
     * Allow an extension to prepend the extension configurations.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function prepend(ContainerBuilder $container)
    {
        $this->prependViews($container);
        $this->prependImageVariations($container);
        $this->prependUniversalDiscoveryWidget($container);
        $this->prependEzDesignConfiguration($container);
        $this->prependAdminUiFormsConfiguration($container);
        $this->prependBazingaJsTranslationConfiguration($container);
        $this->prependJMSTranslation($container);
    }

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    private function prependViews(ContainerBuilder $container): void
    {
        $configFile = __DIR__ . '/../Resources/config/views.yml';
        $config = Yaml::parse(file_get_contents($configFile));
        $container->prependExtensionConfig('ezpublish', $config);
        $container->addResource(new FileResource($configFile));
    }

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    private function prependImageVariations(ContainerBuilder $container)
    {
        $imageConfigFile = __DIR__ . '/../Resources/config/image_variations.yml';
        $config = Yaml::parse(file_get_contents($imageConfigFile));
        $container->prependExtensionConfig('ezpublish', $config);
        $container->addResource(new FileResource($imageConfigFile));
    }

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    private function prependUniversalDiscoveryWidget(ContainerBuilder $container)
    {
        $udwConfigFile = __DIR__ . '/../Resources/config/universal_discovery_widget.yml';
        $config = Yaml::parse(file_get_contents($udwConfigFile));
        $container->prependExtensionConfig('ezpublish', $config);
        $container->addResource(new FileResource($udwConfigFile));
    }

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    private function prependEzDesignConfiguration(ContainerBuilder $container)
    {
        $eZDesignConfigFile = __DIR__ . '/../Resources/config/ezdesign.yml';
        $config = Yaml::parseFile($eZDesignConfigFile);
        $container->prependExtensionConfig('ezdesign', $config['ezdesign']);
        $container->prependExtensionConfig('ezpublish', $config['ezpublish']);
        $container->addResource(new FileResource($eZDesignConfigFile));
    }

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    private function prependAdminUiFormsConfiguration(ContainerBuilder $container)
    {
        $adminUiFormsConfigFile = __DIR__ . '/../Resources/config/admin_ui_forms.yml';
        $config = Yaml::parseFile($adminUiFormsConfigFile);
        $container->prependExtensionConfig('ezpublish', $config);
        $container->addResource(new FileResource($adminUiFormsConfigFile));
    }

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    private function prependBazingaJsTranslationConfiguration(ContainerBuilder $container)
    {
        $configFile = __DIR__ . '/../Resources/config/bazinga_js_translation.yml';
        $config = Yaml::parseFile($configFile);
        $container->prependExtensionConfig('bazinga_js_translation', $config);
        $container->addResource(new FileResource($configFile));
    }

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    private function prependJMSTranslation(ContainerBuilder $container): void
    {
        $container->prependExtensionConfig('jms_translation', [
            'configs' => [
                'ezplatform_admin_ui' => [
                    'dirs' => [
                        __DIR__ . '/../../../src/',
                    ],
                    'output_dir' => __DIR__ . '/../Resources/translations/',
                    'output_format' => 'xliff',
                    'excluded_dirs' => ['Behat', 'Tests', 'node_modules'],
                    'extractors' => ['ez_policy'],
                ],
            ],
        ]);
    }
}
