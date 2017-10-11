<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;

class Builder
{
    /** @var FactoryInterface */
    protected $factory;

    /** @var Provider */
    protected $menuProvider;

    /** @var string */
    protected $identifier;

    /**
     * @param FactoryInterface $factory
     * @param Provider $menuProvider
     * @param string $identifier
     */
    public function __construct(FactoryInterface $factory, Provider $menuProvider, string $identifier)
    {
        $this->factory = $factory;
        $this->menuProvider = $menuProvider;
        $this->identifier = $identifier;
        $this->menuProvider->setCurrent($identifier);
    }

    /**
     * @param array $options
     *
     * @return \Knp\Menu\ItemInterface
     */
    public function createMainMenu(array $options): ItemInterface
    {
        $menu = $this->factory->createItem('root');
        $this->addChildren($menu, $this->menuProvider->getItems());

        return $menu;
    }

    /**
     * @param ItemInterface $menu
     * @param Item[] $items
     */
    protected function addChildren(ItemInterface $menu, array $items)
    {
        foreach ($items as $item) {
            $options = $item->getUrl();
            if (!$item->isEnabled()) {
                $options['extras']['disabled'] = 'disabled';
            }
            if (null !== $item->getIcon()) {
                $options['extras']['icon'] = $item->getIcon();
            }

            $nextItem = $menu->addChild($item->getName(), $options);
            if (!empty($item->getItems())) {
                $this->addChildren($nextItem, $item->getItems());
            }
        }
    }
}
