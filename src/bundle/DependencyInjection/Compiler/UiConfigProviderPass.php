<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUiBundle\DependencyInjection\Compiler;

use EzSystems\EzPlatformAdminUi\UI\Config\Aggregator;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Supplies config Providers to the Aggregator.
 */
class UiConfigProviderPass implements CompilerPassInterface
{
    const TAG_CONFIG_PROVIDER = 'ezplatform.admin_ui.config_provider';

    /**
     * @param ContainerBuilder $container
     *
     * @throws InvalidArgumentException
     * @throws ServiceNotFoundException
     */
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition(Aggregator::class)) {
            return;
        }

        $aggregatorDefinition = $container->getDefinition(Aggregator::class);
        $taggedServiceIds = $container->findTaggedServiceIds(self::TAG_CONFIG_PROVIDER);

        foreach ($taggedServiceIds as $taggedServiceId => $tags) {
            foreach ($tags as $tag) {
                $key = $tag['key'] ?? $taggedServiceId;
                $aggregatorDefinition->addMethodCall('addProvider', [$key, new Reference($taggedServiceId)]);
            }
        }
    }
}
