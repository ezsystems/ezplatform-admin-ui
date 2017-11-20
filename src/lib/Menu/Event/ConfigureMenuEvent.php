<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Menu\Event;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Event triggered after building AdminUI menus. Provides extensibility point for menus' customization.
 */
class ConfigureMenuEvent extends Event
{
    const MAIN_MENU = 'ezplatform_admin_ui.menu_configure.main_menu';
    const CONTENT_SIDEBAR_RIGHT = 'ezplatform_admin_ui.menu_configure.content_sidebar_right';
    const CONTENT_EDIT_SIDEBAR_RIGHT = 'ezplatform_admin_ui.menu_configure.content_edit_sidebar_right';
    const CONTENT_SIDEBAR_LEFT = 'ezplatform_admin_ui.menu_configure.content_sidebar_left';
    const TRASH_SIDEBAR_RIGHT = 'ezplatform_admin_ui.menu_configure.trash_sidebar_right';

    /** @var FactoryInterface */
    private $factory;

    /** @var ItemInterface */
    private $menu;

    /**
     * @param FactoryInterface $factory
     * @param ItemInterface $menu
     */
    public function __construct(FactoryInterface $factory, ItemInterface $menu)
    {
        $this->factory = $factory;
        $this->menu = $menu;
    }

    /**
     * @return FactoryInterface
     */
    public function getFactory(): FactoryInterface
    {
        return $this->factory;
    }

    /**
     * @return ItemInterface
     */
    public function getMenu(): ItemInterface
    {
        return $this->menu;
    }
}
