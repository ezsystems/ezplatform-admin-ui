<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Tests\Menu;

use EzSystems\EzPlatformAdminUi\Menu\MainMenuBuilder;
use Knp\Menu\MenuItem;
use PHPUnit\Framework\TestCase;
use EzSystems\EzPlatformAdminUi\Menu\MenuItemFactory;
use eZ\Publish\API\Repository\PermissionResolver;
use eZ\Publish\Core\MVC\ConfigResolverInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

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
            [MainMenuBuilder::ITEM_CONTENT, [], new MenuItem(MainMenuBuilder::ITEM_CONTENT, $knpFactory)],
            [MainMenuBuilder::ITEM_ADMIN, [], new MenuItem(MainMenuBuilder::ITEM_ADMIN, $knpFactory)],
        ];

        $this->factory = $this->createMock(MenuItemFactory::class);
        $this->factory->method('createItem')->willReturnMap($itemMap);

        $this->eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $this->configResolver = $this->createMock(ConfigResolverInterface::class);
        $this->configResolver->method('getParameter')->willReturnMap($parameterMap);
        $this->permissionResolver = $this->createMock(PermissionResolver::class);
    }

    protected function tearDown(): void
    {
        unset($this->factory, $this->eventDispatcher, $this->configResolver, $this->permissionResolver);
    }

    public function testCreateMenuForUserWithAdministratePolicy()
    {
        $accessMap = [
            ['setup', 'administrate', null, true],
        ];

        $this->permissionResolver->method('hasAccess')->willReturnMap($accessMap);

        $menuBuilder = new MainMenuBuilder($this->factory, $this->eventDispatcher, $this->configResolver, $this->permissionResolver);
        $menu = $menuBuilder->createStructure([]);

        $children = $menu->getChildren();

        $this->assertArrayHasKey(MainMenuBuilder::ITEM_CONTENT, $children);
        $this->assertArrayHasKey(MainMenuBuilder::ITEM_ADMIN, $children);
    }

    public function testCreateMenuForUserWithoutAdministratePolicy()
    {
        $accessMap = [
            ['setup', 'administrate', null, false],
        ];

        $this->permissionResolver->method('hasAccess')->willReturnMap($accessMap);

        $menuBuilder = new MainMenuBuilder($this->factory, $this->eventDispatcher, $this->configResolver, $this->permissionResolver);
        $menu = $menuBuilder->createStructure([]);

        $children = $menu->getChildren();

        $this->assertArrayHasKey(MainMenuBuilder::ITEM_CONTENT, $children);
        $this->assertArrayHasKey(MainMenuBuilder::ITEM_ADMIN, $children);
    }
}
