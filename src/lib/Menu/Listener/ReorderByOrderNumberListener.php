<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Menu\Listener;

use EzSystems\EzPlatformAdminUi\Menu\Event\ConfigureMenuEvent;
use Knp\Menu\ItemInterface;

final class ReorderByOrderNumberListener
{
    public function reorderMenuItems(ConfigureMenuEvent $event): void
    {
        $menu = $event->getMenu();

        $this->recursiveReorderMenuItems($menu);
    }

    private function recursiveReorderMenuItems(ItemInterface $menuItem): void
    {
        $menuItemList = [];
        $unorderedMenuItemsList = [];

        foreach ($menuItem->getChildren() as $nestedMenuItem) {
            if ($nestedMenuItem->hasChildren()) {
                $this->recursiveReorderMenuItems($nestedMenuItem);
            }

            $orderNumber = $nestedMenuItem->getExtra('orderNumber');

            if ($orderNumber === null) {
                $unorderedMenuItemsList[] = $nestedMenuItem;
            } else {
                $menuItemList[$orderNumber][] = $nestedMenuItem;
            }
        }

        ksort($menuItemList);

        $menuItemList[] = $unorderedMenuItemsList;

        $menuItem->reorderChildren(
            array_map(static function (ItemInterface $item): string {
                return $item->getName();
            }, array_merge(...$menuItemList))
        );
    }
}
