<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Menu;

use EzSystems\EzPlatformAdminUi\Menu\Event\ConfigureMenuEvent;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

abstract class AbstractBuilder
{
    /** @var FactoryInterface */
    protected $factory;

    /** @var EventDispatcherInterface */
    protected $eventDispatcher;

    /**
     * @param FactoryInterface $factory
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(FactoryInterface $factory, EventDispatcherInterface $eventDispatcher)
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
    protected function createMenuItem(string $id, array $options)
    {
        $defaults = [
            'extras' => ['translation_domain' => 'menu'],
        ];

        return $this->factory->createItem($id, array_merge_recursive($defaults, $options));
    }

    /**
     * @param string $name
     * @param Event $event
     */
    protected function dispatchMenuEvent(string $name, Event $event): void
    {
        $this->eventDispatcher->dispatch($name, $event);
    }

    /**
     * @param ItemInterface $menu
     *
     * @return ConfigureMenuEvent
     */
    protected function createConfigureMenuEvent(ItemInterface $menu): ConfigureMenuEvent
    {
        return new ConfigureMenuEvent($this->factory, $menu);
    }

    /**
     * @param array $options
     *
     * @return ItemInterface
     */
    public function build(array $options): ItemInterface
    {
        $menu = $this->createStructure($options);

        $this->dispatchMenuEvent($this->getConfigureEventName(), $this->createConfigureMenuEvent($menu));

        return $menu;
    }

    abstract protected function getConfigureEventName(): string;

    abstract protected function createStructure(array $options): ItemInterface;
}
