<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Menu;

interface MenuItemInterface
{
    public function addItem(Item $item);

    public function getItem(string $identifier): Item;

    public function getItems();
}
