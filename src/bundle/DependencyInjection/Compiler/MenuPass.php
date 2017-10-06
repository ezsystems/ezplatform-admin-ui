<?php

namespace EzPlatformAdminUiBundle\DependencyInjection\Compiler;


use EzPlatformAdminUi\Menu\Item;
use EzPlatformAdminUi\Menu\Registry;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class MenuPass implements CompilerPassInterface
{
    const MENU_TAG = 'ezplatform.menu';
    const MENU_ITEM_TAG = 'ezplatform.menu.item';

    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition(Registry::class)) {
             return;
        }

        $menuRegistryDefinition = $container->getDefinition(Registry::class);
        $items = $container->findTaggedServiceIds(static::MENU_ITEM_TAG);

        foreach ($items as $id => $child) {
            $tag = array_pop($child);
            $itemDefinition = $container->getDefinition($id);
            if (isset($tag['priority'])) {
                $itemDefinition->addMethodCall('setPriority', [$tag['priority']]);
            }
            $parent = $container->getDefinition($tag['parent']);
            $parent->addMethodCall('addItem', [new Reference($id)]);
        }

        $menus = $container->findTaggedServiceIds(static::MENU_TAG);
        foreach ($menus as $id => $menu) {
            $menuRegistryDefinition->addMethodCall('addMenu', [new Reference($id)]);
        }
    }
}
