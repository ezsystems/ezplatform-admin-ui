<?php

namespace EzSystems\EzPlatformAdminUi\Menu;

use EzSystems\EzPlatformAdminUi\Exception\MenuItemExists;
use EzSystems\EzPlatformAdminUi\Exception\MenuItemNotExists;

class Menu implements MenuItemInterface
{
    /** @var string */
    public $identifier;

    /** @var Item[] */
    public $items;

    /**
     * @param string $identifier
     * @param array $items
     */
    public function __construct(string $identifier, array $items = [])
    {
        $this->identifier = $identifier;
    }

    public function addItem(Item $item)
    {
        if (isset($this->items[$item->getIdentifier()])) {
            throw new MenuItemExists(sprintf('Menu Item %s already exists.', $item->getIdentifier()));
        }

        $this->items[$item->getIdentifier()] = $item;
    }

    public function getItem(string $identifier): Item
    {
        if (!isset($this->items[$identifier])) {
            throw new MenuItemNotExists(sprintf('No Menu Item %s found', $identifier));
        }

        return $this->items[$identifier];
    }

    public function getItems()
    {
        return $this->items;
    }
}
