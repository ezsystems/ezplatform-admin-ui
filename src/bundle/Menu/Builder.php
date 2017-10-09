<?php

namespace EzSystems\EzPlatformAdminUiBundle\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;

class Builder
{
    /** @var FactoryInterface */
    protected $factory;

    /** @var array */
    protected $menuItems;

    /**
     * @param FactoryInterface $factory
     */
    public function __construct(FactoryInterface $factory,  $menuItems)
    {
        $this->factory = $factory;
        $this->menuItems = $this->sortItems($menuItems);
    }

    /**
     * @param array $menuItems
     *
     * @return array
     */
    public function sortItems(array $menuItems): array
    {
        foreach ($menuItems['children'] as &$child) {
            if (isset($child['children'])) {
                $child = $this->sortItems($child);
            }
        }

        uasort($menuItems['children'], function ($a, $b) {
            return $a['priority'] <=> $b['priority'];
        });

        return $menuItems;
    }

    /**
     * @param array $options
     *
     * @return \Knp\Menu\ItemInterface
     */
    public function createMainMenu(array $options): ItemInterface
    {
        $menu = $this->factory->createItem('root');
        $this->addChildren($menu, $this->menuItems);

        return $menu;
    }

    /**
     * @param ItemInterface $menu
     * @param array $item
     */
    protected function addChildren(ItemInterface $menu, array $item)
    {
        foreach ($item['children'] as $child) {
            $options = [];
            if (isset($child['route'])) {
                $options['route'] = $child['route'];
            }
            $nextItem = $menu->addChild($child['name'], $options);
            if (isset($child['children'])) {
                $this->addChildren($nextItem, $child);
            }
        }
    }
}
