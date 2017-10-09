<?php

namespace EzSystems\EzPlatformAdminUi\Menu;

use EzSystems\EzPlatformAdminUi\Exception\MenuItemExists;
use EzSystems\EzPlatformAdminUi\Exception\MenuItemNotExists;

class Registry
{
    protected $menus;

    public function addMenu(Menu $menu)
    {
        if (isset($this->menus[$menu->identifier])) {
            throw new MenuItemExists(sprintf('Menu Item %s already exists in the registry', $menu->identifier));
        }

        $this->menus[$menu->identifier] = $menu;
    }

    public function getMenu(string $identifier): Menu
    {
        if (!isset($this->menus[$identifier])) {
            throw new MenuItemNotExists(sprintf('No Menu Item %s found in the registry', $identifier));
        }

        return $this->menus[$identifier];
    }

    public function getMenus(): array
    {
        return $this->menus;
    }
}
