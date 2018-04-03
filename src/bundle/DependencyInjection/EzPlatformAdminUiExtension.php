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
     * @param ContainerBuilder $container
     */
    public function prepend(ContainerBuilder $container)
    {
        $this->prependViews($container);
        $this->prependImageVariations($container);
    }

    /**
     * @param ContainerBuilder $container
     */
    private function prependViews(ContainerBuilder $container): void
    {
        $configFile = __DIR__ . '/../Resources/config/views.yml';
        $config = Yaml::parse(file_get_contents($configFile));
        $container->prependExtensionConfig('ezpublish', $config);
        $container->addResource(new FileResource($configFile));
    }

    /**
     * @param ContainerBuilder $container
     */
    private function prependImageVariations(ContainerBuilder $container)
    {
        $imageConfigFile = __DIR__ . '/../Resources/config/image_variations.yml';
        $config = Yaml::parse(file_get_contents($imageConfigFile));
        $container->prependExtensionConfig('ezpublish', $config);
        $container->addResource(new FileResource($imageConfigFile));
    }
}
