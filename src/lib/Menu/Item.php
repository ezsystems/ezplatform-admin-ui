<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Menu;

use EzSystems\EzPlatformAdminUi\Exception\MenuItemExists;
use EzSystems\EzPlatformAdminUi\Exception\MenuItemNotExists;

class Item implements MenuItemInterface
{
    /** @var string */
    protected $identifier;

    /** @var string */
    protected $name;

    /** @var array */
    protected $url;

    /** @var string */
    protected $icon;

    /** @var int */
    protected $priority;

    /** @var bool */
    protected $enabled;

    /** @var array */
    protected $items;

    /**
     * @param string $identifier
     * @param string $name
     * @param array $url
     * @param string $icon
     * @param bool $enabled
     */
    public function __construct(string $identifier, string $name = null, array $url = [], string $icon = '', $enabled = true)
    {
        $this->identifier = $identifier;
        $this->name = $name ?? ucfirst($identifier);
        $this->url = $url;
        $this->icon = $icon;
        $this->enabled = $enabled;
    }

    /**
     * @return string
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * @return array
     */
    public function getUrl(): array
    {
        return $this->url;
    }

    /**
     * @return string
     */
    public function getIcon(): string
    {
        return $this->icon;
    }

    /**
     * @return null|int
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * @param int $priority
     */
    public function setPriority(int $priority)
    {
        $this->priority = $priority;
    }

    /**
     * @param Item $item
     *
     * @throws MenuItemExists
     */
    public function addItem(Item $item)
    {
        if (isset($this->items[$item->identifier])) {
            throw new MenuItemExists(sprintf('Menu Item %s already exists.', $item->identifier));
        }

        $this->items[$item->identifier] = $item;
    }

    public function getItem(string $identifier): Item
    {
        if (!isset($this->items[$identifier])) {
            throw new MenuItemNotExists(sprintf('No Menu Item %s found', $identifier));
        }

        return $this->items[$identifier];
    }

    /**
     * @return array
     */
    public function getItems()
    {
        return $this->items;
    }
}
