<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\AdminUi\Menu\Admin;

use Ibexa\AdminUi\Menu\Event\ConfigureMenuEvent;
use Ibexa\AdminUi\Menu\MainMenuBuilder;
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
}

class_alias(ReorderMenuListener::class, 'EzSystems\EzPlatformAdminUi\Menu\Admin\ReorderMenuListener');
