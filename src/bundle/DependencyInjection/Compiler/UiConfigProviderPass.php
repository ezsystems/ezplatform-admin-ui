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
use Symfony\Component\DependencyInjection\Reference;

/**
 * Supplies config Providers to the Aggregator.
 */
class UiConfigProviderPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition(Aggregator::class)) {
            return;
        }

        $aggregatorDefinition = $container->getDefinition(Aggregator::class);
        $taggedServiceIds = $container->findTaggedServiceIds('ezplatform.admin_ui.config_provider');
        foreach ($taggedServiceIds as $taggedServiceId => $tags) {
            foreach ($tags as $tag) {
                $key = isset($tag['key']) ? $tag['key'] : $taggedServiceId;
                $aggregatorDefinition->addMethodCall('addProvider', [$key, new Reference($taggedServiceId)]);
            }
        }
    }
}
