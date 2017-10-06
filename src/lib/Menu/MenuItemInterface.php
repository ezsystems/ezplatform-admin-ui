<?php

namespace EzPlatformAdminUi\Menu;

interface MenuItemInterface
{
    public function addItem(Item $item);

    public function getItem(string $identifier): Item;

    public function getItems();
}
