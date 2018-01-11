<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUiBundle\DependencyInjection\Compiler;

use EzSystems\EzPlatformAdminUi\Tab\TabRegistry;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * {@inheritdoc}
 */
class TabPass implements CompilerPassInterface
{
    const TAG_TAB = 'ezplatform.tab';

    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition(TabRegistry::class)) {
            return;
        }

        $tabRegistryDefinition = $container->getDefinition(TabRegistry::class);
        $tabIds = $container->findTaggedServiceIds(static::TAG_TAB);

        foreach ($tabIds as $id => $tab) {
            $tabDefinition = $container->getDefinition($id);
            $tag = $tabDefinition->getTag(static::TAG_TAB);

            foreach (array_column($tag, 'group') as $group) {
                $tabRegistryDefinition->addMethodCall('addTab', [new Reference($id), $group]);
            }
        }
    }
}
