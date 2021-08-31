<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Tests\Menu;

use eZ\Publish\API\Repository\PermissionResolver;
use eZ\Publish\Core\MVC\ConfigResolverInterface;
use eZ\Publish\Core\MVC\Symfony\Security\UserInterface;
use EzSystems\EzPlatformAdminUi\Menu\MainMenuBuilder;
use EzSystems\EzPlatformAdminUi\Menu\MenuItemFactory;
use Knp\Menu\MenuItem;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\TestBrowserToken;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class MainMenuBuilerTest extends TestCase
{
    /** @var \EzSystems\EzPlatformAdminUi\Menu\MenuItemFactory */
    private $factory;

    /** @var \Symfony\Component\EventDispatcher\EventDispatcherInterface */
    private $eventDispatcher;

    /** @var \eZ\Publish\Core\MVC\ConfigResolverInterface */
    private $configResolver;

    /** @var \eZ\Publish\API\Repository\PermissionResolver */
    private $permissionResolver;

    /** @var \Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface */
    private $tokenStorage;

    protected function setUp(): void
    {
        $knpFactory = $this->createMock(\Knp\Menu\FactoryInterface::class);

        $parameterMap = [
            ['location_ids.content_structure', null, null, 5],
            ['location_ids.media', null, null, 10],
            ['location_ids.users', null, null, 20],
        ];

        $itemMap = [
            ['root', [], new MenuItem('root', $knpFactory)],
            [
                MainMenuBuilder::ITEM_DASHBOARD,
                [
                    'route' => 'ezplatform.dashboard',
                    'attributes' => [
                        'data-tooltip-placement' => 'right',
                        'data-tooltip-extra-class' => 'ibexa-tooltip--info-neon',
                    ],
                    'extras' => [
                        'icon' => 'dashboard-clean',
                        'orderNumber' => 20,
                    ],
                ],
                new MenuItem(MainMenuBuilder::ITEM_DASHBOARD, $knpFactory),
            ],
            [
                MainMenuBuilder::ITEM_CONTENT,
                [
                    'attributes' => [
                        'data-tooltip-placement' => 'right',
                        'data-tooltip-extra-class' => 'ibexa-tooltip--info-neon',
                    ],
                    'extras' => [
                        'icon' => 'hierarchy',
                        'orderNumber' => 40,
                    ],
                ],
                new MenuItem(MainMenuBuilder::ITEM_CONTENT, $knpFactory),
            ],
            [
                MainMenuBuilder::ITEM_ADMIN,
                [
                    'attributes' => [
                        'data-tooltip-placement' => 'right',
                        'data-tooltip-extra-class' => 'ibexa-tooltip--info-neon',
                    ],
                    'extras' => [
                        'separate' => true,
                        'bottom_item' => true,
                        'icon' => 'settings-block',
                        'orderNumber' => 140,
                    ],
                ],
                new MenuItem(MainMenuBuilder::ITEM_ADMIN, $knpFactory),
            ],
            [
                MainMenuBuilder::ITEM_BOOKMARKS,
                [
                    'route' => 'ezplatform.bookmark.list',
                    'attributes' => [
                        'data-tooltip-placement' => 'right',
                        'data-tooltip-extra-class' => 'ibexa-tooltip--info-neon',
                    ],
                    'extras' => [
                        'bottom_item' => true,
                        'icon' => 'bookmark',
                        'orderNumber' => 160,
                    ],
                ],
                new MenuItem(MainMenuBuilder::ITEM_BOOKMARKS, $knpFactory),
            ],
            [
                MainMenuBuilder::ITEM_TRASH,
                [
                    'route' => 'ezplatform.trash.list',
                    'attributes' => [
                        'data-tooltip-placement' => 'right',
                        'data-tooltip-extra-class' => 'ibexa-tooltip--info-neon',
                    ],
                    'extras' => [
                        'bottom_item' => true,
                        'icon' => 'trash',
                        'orderNumber' => 180,
                    ],
                ],
                new MenuItem(MainMenuBuilder::ITEM_TRASH, $knpFactory),
            ],
        ];
        foreach (MainMenuBuilder::ITEM_ADMIN_OPTIONS as $id => $options) {
            $itemMap[] = [
                $id,
                $options,
                new MenuItem($id, $knpFactory),
            ];
        }

        $this->factory = $this->createMock(MenuItemFactory::class);
        $this->factory->method('createItem')->willReturnMap($itemMap);

        $this->eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $this->configResolver = $this->createMock(ConfigResolverInterface::class);
        $this->configResolver->method('getParameter')->willReturnMap($parameterMap);
        $this->permissionResolver = $this->createMock(PermissionResolver::class);
        $this->tokenStorage = $this->createMock(TokenStorageInterface::class);

        $token = new TestBrowserToken([], $this->createMock(UserInterface::class));
        $this->tokenStorage->method('getToken')->willReturn($token);
    }

    protected function tearDown(): void
    {
        unset($this->factory, $this->eventDispatcher, $this->configResolver, $this->permissionResolver, $this->tokenStorage);
    }

    public function testCreateMenuForUserWithAdministratePolicy()
    {
        $accessMap = [
            ['setup', 'administrate', null, true],
        ];

        $this->permissionResolver->method('hasAccess')->willReturnMap($accessMap);

        $menuBuilder = new MainMenuBuilder(
            $this->factory,
            $this->eventDispatcher,
            $this->configResolver,
            $this->permissionResolver,
            $this->tokenStorage
        );
        $menu = $menuBuilder->createStructure([]);

        $children = $menu->getChildren();

        $this->assertMenuHasAllItems($children);
    }

    public function testCreateMenuForUserWithoutAdministratePolicy()
    {
        $accessMap = [
            ['setup', 'administrate', null, false],
        ];

        $this->permissionResolver->method('hasAccess')->willReturnMap($accessMap);

        $menuBuilder = new MainMenuBuilder(
            $this->factory,
            $this->eventDispatcher,
            $this->configResolver,
            $this->permissionResolver,
            $this->tokenStorage
        );
        $menu = $menuBuilder->createStructure([]);

        $children = $menu->getChildren();
        $this->assertMenuHasAllItems($children);
    }

    private function assertMenuHasAllItems(array $menu): void
    {
        $this->assertArrayHasKey(MainMenuBuilder::ITEM_DASHBOARD, $menu);
        $this->assertArrayHasKey(MainMenuBuilder::ITEM_CONTENT, $menu);
        $this->assertArrayHasKey(MainMenuBuilder::ITEM_ADMIN, $menu);
        $this->assertArrayHasKey(MainMenuBuilder::ITEM_BOOKMARKS, $menu);
        $this->assertArrayHasKey(MainMenuBuilder::ITEM_TRASH, $menu);
    }
}
