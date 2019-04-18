<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Menu\Admin;

use EzSystems\EzPlatformAdminUi\Menu\Event\ConfigureMenuEvent;
use EzSystems\EzPlatformAdminUi\Menu\MainMenuBuilder;
use Knp\Menu\Util\MenuManipulator;

class ReorderMenuListener
{
    /**
     * @param \EzSystems\EzPlatformAdminUi\Menu\Event\ConfigureMenuEvent $event
     */
    public function moveAdminToLast(ConfigureMenuEvent $event): void
    {
        $menu = $event->getMenu();

        if (!$menu->getChild(MainMenuBuilder::ITEM_ADMIN)) {
            return;
        }
        $manipulator = new MenuManipulator();
        $manipulator->moveToLastPosition($menu[MainMenuBuilder::ITEM_ADMIN]);
    }

    /**
     * @param \EzSystems\EzPlatformAdminUi\Menu\Event\ConfigureMenuEvent $event
     */
    public function reorderMenuItems(ConfigureMenuEvent $event): void
    {
        $menu = $event->getMenu();
        $menuOrderArray = [];
        $addLast = [];
        $alreadyTaken = [];

        foreach ($menu->getChildren() as $key => $menuItem) {
            if ($menuItem->hasChildren()) {
                $this->reorderMenuItems($menuItem);
            }
            $orderNumber = $menuItem->getExtra('orderNumber');

            if ($orderNumber != null) {
                if (!isset($menuOrderArray[$orderNumber])) {
                    $menuOrderArray[$orderNumber] = $menuItem->getName();
                } else {
                    $alreadyTaken[$orderNumber] = $menuItem->getName();
                }
            } else {
                $addLast[] = $menuItem->getName();
            }
        }
        ksort($menuOrderArray);

        if (count($alreadyTaken)) {
            foreach ($alreadyTaken as $key => $value) {
                $keysArray = array_keys($menuOrderArray);
                $position = array_search($key, $keysArray);

                if ($position === false) {
                    continue;
                }

                $menuOrderArray = array_merge(
                    array_slice($menuOrderArray, 0, $position),
                    [$value],
                    array_slice($menuOrderArray, $position)
                );
            }
        }
        ksort($menuOrderArray);

        if (count($addLast)) {
            foreach ($addLast as $key => $value) {
                $menuOrderArray[] = $value;
            }
        }

        if (count($menuOrderArray)) {
            $menu->reorderChildren($menuOrderArray);
        }
    }
}
