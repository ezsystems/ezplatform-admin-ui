<?php

namespace EzPlatformAdminUiBundle\DependencyInjection\Compiler;


use EzPlatformAdminUi\Tab\TabRegistry;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * {@inheritDoc}
 */
class TabPass implements CompilerPassInterface
{
    const TAG_TAB = 'ezplatform.tab';

    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition(TabRegistry::class)) {
             return;
        }

        $tabRegistryDefinition = $container->getDefinition(TabRegistry::class);
        $tabIds = $container->findTaggedServiceIds(static::TAG_TAB);

        foreach ($tabIds as $id => $tab) {
            $tabDefinition = $container->getDefinition($id);
            $tag = $tabDefinition->getTag(static::TAG_TAB);

            foreach (array_column($tag,'group') as $group) {
                $tabRegistryDefinition->addMethodCall('addTab', [new Reference($id), $group]);
            }
        }
    }
}
