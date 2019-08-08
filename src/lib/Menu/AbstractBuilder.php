<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Menu;

use EzSystems\EzPlatformAdminUi\Menu\Event\ConfigureMenuEvent;
use Knp\Menu\ItemInterface;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Base builder for extendable AdminUI menus.
 */
abstract class AbstractBuilder
{
    /** @var MenuItemFactory */
    protected $factory;

    /** @var EventDispatcherInterface */
    protected $eventDispatcher;

    /**
     * @param MenuItemFactory $factory
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(MenuItemFactory $factory, EventDispatcherInterface $eventDispatcher)
    {
        $this->factory = $factory;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param string $id
     * @param array $options
     *
     * @return ItemInterface
     */
    protected function createMenuItem(string $id, array $options): ?ItemInterface
    {
        return $this->factory->createItem($id, $options);
    }

    /**
     * @param string $name
     * @param Event $event
     */
    protected function dispatchMenuEvent(string $name, Event $event): void
    {
        $this->eventDispatcher->dispatch($event, $name);
    }

    /**
     * @param ItemInterface $menu
     *
     * @return ConfigureMenuEvent
     */
    protected function createConfigureMenuEvent(ItemInterface $menu, array $options = []): ConfigureMenuEvent
    {
        return new ConfigureMenuEvent($this->factory, $menu, $options);
    }

    /**
     * @param array $options
     *
     * @return ItemInterface
     */
    public function build(array $options): ItemInterface
    {
        $menu = $this->createStructure($options);

        $this->dispatchMenuEvent($this->getConfigureEventName(), $this->createConfigureMenuEvent($menu, $options));

        return $menu;
    }

    abstract protected function getConfigureEventName(): string;

    abstract protected function createStructure(array $options): ItemInterface;
}
