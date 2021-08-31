<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Tests\Menu;

use EzSystems\EzPlatformAdminUi\Menu\Event\ConfigureMenuEvent;
use EzSystems\EzPlatformAdminUi\Menu\Listener\ReorderByOrderNumberListener;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Knp\Menu\MenuFactory;
use PHPUnit\Framework\TestCase;

final class ReorderByOrderNumberListenerTest extends TestCase
{
    public function testUnorderedMenuListUntouched(): void
    {
        $factory = new MenuFactory();
        $menu = $this->recursiveCreateMenuChildren(
            $factory->createItem('menu'),
            $factory, [
                ['name' => 'first'],
                ['name' => 'second'],
                ['name' => 'third'],
                ['name' => 'fourth'],
                ['name' => 'fifth'],
            ]
        );

        $eventMock = $this->getConfigureMenuEventMock($menu);

        $listener = new ReorderByOrderNumberListener();
        $listener->reorderMenuItems($eventMock);

        $expected = [
            'first',
            'second',
            'third',
            'fourth',
            'fifth',
        ];

        $this->assertEquals($expected, $this->getChildrenNames($menu));
    }

    public function testOrderedMenuList(): void
    {
        $factory = new MenuFactory();
        $menu = $this->recursiveCreateMenuChildren(
            $factory->createItem('menu'),
            $factory, [
                ['name' => 'first', 'order' => 100],
                ['name' => 'second', 'order' => 10],
                ['name' => 'third', 'order' => 55],
                ['name' => 'fourth', 'order' => 30],
                ['name' => 'fifth', 'order' => 75],
            ]
        );

        $eventMock = $this->getConfigureMenuEventMock($menu);

        $listener = new ReorderByOrderNumberListener();
        $listener->reorderMenuItems($eventMock);

        $expected = [
            'second',
            'fourth',
            'third',
            'fifth',
            'first',
        ];

        $this->assertEquals($expected, $this->getChildrenNames($menu));
    }

    public function testSameOrderMenuListUntouched(): void
    {
        $factory = new MenuFactory();
        $menu = $this->recursiveCreateMenuChildren(
            $factory->createItem('menu'),
            $factory, [
                ['name' => 'first', 'order' => 50],
                ['name' => 'second', 'order' => 10],
                ['name' => 'third', 'order' => 10],
                ['name' => 'fourth', 'order' => 10],
                ['name' => 'fifth', 'order' => 50],
            ]
        );

        $eventMock = $this->getConfigureMenuEventMock($menu);

        $listener = new ReorderByOrderNumberListener();
        $listener->reorderMenuItems($eventMock);

        $expected = [
            'second',
            'third',
            'fourth',
            'first',
            'fifth',
        ];

        $this->assertEquals($expected, $this->getChildrenNames($menu));
    }

    public function testAppendUnorderedMenuListAtTheEnd(): void
    {
        $factory = new MenuFactory();
        $menu = $this->recursiveCreateMenuChildren(
            $factory->createItem('menu'),
            $factory, [
                ['name' => 'first', 'order' => 100],
                ['name' => 'unordered'],
                ['name' => 'second', 'order' => 10],
                ['name' => 'third', 'order' => 55],
                ['name' => 'another'],
                ['name' => 'fourth', 'order' => 30],
                ['name' => 'fifth', 'order' => 75],
            ]
        );

        $eventMock = $this->getConfigureMenuEventMock($menu);

        $listener = new ReorderByOrderNumberListener();
        $listener->reorderMenuItems($eventMock);

        $expected = [
            'second',
            'fourth',
            'third',
            'fifth',
            'first',
            'unordered',
            'another',
        ];

        $this->assertEquals($expected, $this->getChildrenNames($menu));
    }

    public function testNestedOrderedMenuList(): void
    {
        $factory = new MenuFactory();
        $menu = $this->recursiveCreateMenuChildren(
            $factory->createItem('menu'),
            $factory, [
                ['name' => 'first', 'order' => 100],
                ['name' => 'unordered'],
                ['name' => 'second', 'order' => 10],
                ['name' => 'third', 'order' => 55],
                ['name' => 'another', 'children' => [
                    ['name' => 'first_child', 'order' => 100],
                    ['name' => 'unordered_child'],
                    ['name' => 'second_child', 'order' => 10],
                    ['name' => 'third_child', 'order' => 55],
                ]],
                ['name' => 'fourth', 'order' => 30],
                ['name' => 'fifth', 'order' => 75],
            ]
        );

        $eventMock = $this->getConfigureMenuEventMock($menu);

        $listener = new ReorderByOrderNumberListener();
        $listener->reorderMenuItems($eventMock);

        $expected = [
            'second',
            'fourth',
            'third',
            'fifth',
            'first',
            'unordered',
            'another',
        ];

        $expectedNested = [
            'second_child',
            'third_child',
            'first_child',
            'unordered_child',
        ];

        $this->assertEquals($expected, $this->getChildrenNames($menu));
        $this->assertEquals($expectedNested, $this->getChildrenNames($menu->getChild('another')));
    }

    private function getConfigureMenuEventMock(ItemInterface $menu): ConfigureMenuEvent
    {
        $mock = $this->createMock(ConfigureMenuEvent::class);
        $mock
            ->expects($this->any())
            ->method('getMenu')
            ->willReturn($menu);

        return $mock;
    }

    private function recursiveCreateMenuChildren(
        ItemInterface $menu,
        FactoryInterface $factory,
        array $items
    ): ItemInterface {
        foreach ($items as $item) {
            $child = $factory->createItem($item['name']);
            $child->setExtra('orderNumber', $item['order'] ?? null);

            $this->recursiveCreateMenuChildren($child, $factory, $item['children'] ?? []);

            $menu->addChild($child);
        }

        return $menu;
    }

    private function getChildrenNames(ItemInterface $item): array
    {
        return array_map(static function (ItemInterface $item): string {
            return $item->getName();
        }, array_values($item->getChildren()));
    }
}
