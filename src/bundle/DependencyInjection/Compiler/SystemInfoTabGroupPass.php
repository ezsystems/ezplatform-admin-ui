<?php

namespace EzPlatformAdminUiBundle\DependencyInjection\Compiler;


use EzPlatformAdminUi\Tab\TabGroup;
use EzPlatformAdminUi\Tab\TabRegistry;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * {@inheritDoc}
 */
class SystemInfoTabGroupPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition(TabRegistry::class)) {
            return;
        }

        $tabRegistry = $container->getDefinition(TabRegistry::class);
        $tabGroupDefinition = new Definition(TabGroup::class, ['systeminfo']);
        $tabRegistry->addMethodCall('addTabGroup', [$tabGroupDefinition]);
    }
}
