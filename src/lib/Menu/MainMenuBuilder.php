<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Menu;

use eZ\Publish\API\Repository\PermissionResolver;
use eZ\Publish\Core\MVC\ConfigResolverInterface;
use EzSystems\EzPlatformAdminUi\Menu\Event\ConfigureMenuEvent;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * KnpMenuBundle Menu Builder service implementation for AdminUI top menu.
 *
 * @see https://symfony.com/doc/current/bundles/KnpMenuBundle/menu_builder_service.html
 */
class MainMenuBuilder extends AbstractBuilder implements TranslationContainerInterface
{
    /* Main Menu / Content */
    const ITEM_CONTENT = 'main__content';
    const ITEM_CONTENT__CONTENT_STRUCTURE = 'main__content__content_structure';
    const ITEM_CONTENT__MEDIA = 'main__content__media';
    const ITEM_CONTENT__LINK_MANAGER = 'main__content__linkmanager';

    /* Main Menu / Admin */
    const ITEM_ADMIN = 'main_admin';
    const ITEM_ADMIN__SYSTEMINFO = 'main__admin__systeminfo';
    const ITEM_ADMIN__SECTIONS = 'main__admin__sections';
    const ITEM_ADMIN__ROLES = 'main__admin__roles';
    const ITEM_ADMIN__LANGUAGES = 'main__admin__languages';
    const ITEM_ADMIN__CONTENT_TYPES = 'main__admin__content_types';
    const ITEM_ADMIN__USERS = 'main__admin__users';
    const ITEM_ADMIN__STATE = 'main__admin__state';

    /** @var ConfigResolverInterface */
    private $configResolver;

    /** @var PermissionResolver */
    private $permissionResolver;

    /**
     * @param MenuItemFactory $factory
     * @param EventDispatcherInterface $eventDispatcher
     * @param ConfigResolverInterface $configResolver
     * @param PermissionResolver $permissionResolver
     */
    public function __construct(
        MenuItemFactory $factory,
        EventDispatcherInterface $eventDispatcher,
        ConfigResolverInterface $configResolver,
        PermissionResolver $permissionResolver
    ) {
        parent::__construct($factory, $eventDispatcher);

        $this->configResolver = $configResolver;
        $this->permissionResolver = $permissionResolver;
    }

    protected function getConfigureEventName(): string
    {
        return ConfigureMenuEvent::MAIN_MENU;
    }

    /**
     * @param array $options
     *
     * @return ItemInterface
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\BadStateException
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
    public function createStructure(array $options): ItemInterface
    {
        /** @var ItemInterface|ItemInterface[] $menu */
        $menu = $this->factory->createItem('root');

        $contentMenuItems = $this->getContentMenuItems();
        $menu->addChild($this->factory->createItem(self::ITEM_CONTENT, []));
        $menu[self::ITEM_CONTENT]->setChildren($contentMenuItems);

        $adminMenuItems = $this->getAdminMenuItems();
        $menu->addChild($this->factory->createItem(self::ITEM_ADMIN, []));
        $menu[self::ITEM_ADMIN]->setChildren($adminMenuItems);

        return $menu;
    }

    /**
     * @return array
     */
    private function getContentMenuItems(): array
    {
        $menuItems = [];

        $rootContentId = $this->configResolver->getParameter('location_ids.content_structure');
        $rootMediaId = $this->configResolver->getParameter('location_ids.media');

        $contentStructureItem = $this->factory->createLocationMenuItem(
            self::ITEM_CONTENT__CONTENT_STRUCTURE,
            $rootContentId,
            [
                'label' => self::ITEM_CONTENT__CONTENT_STRUCTURE,
                'extras' => [
                    'routes' => [
                        'search' => 'ezplatform.search',
                        'trash' => 'ezplatform.trash.list',
                    ],
                ],
            ]
        );
        $mediaItem = $this->factory->createLocationMenuItem(
            self::ITEM_CONTENT__MEDIA,
            $rootMediaId,
            ['label' => self::ITEM_CONTENT__MEDIA]
        );
        $linkManagerItem = $this->createLinkManagerMenuItem();

        if (null !== $contentStructureItem) {
            $menuItems[$contentStructureItem->getName()] = $contentStructureItem;
        }

        if (null !== $mediaItem) {
            $menuItems[$mediaItem->getName()] = $mediaItem;
        }

        if (null !== $linkManagerItem) {
            $menuItems[$linkManagerItem->getName()] = $linkManagerItem;
        }

        return $menuItems;
    }

    /**
     * @return array
     */
    private function getAdminMenuItems(): array
    {
        $menuItems = [];

        if ($this->permissionResolver->hasAccess('setup', 'system_info')) {
            $menuItems[self::ITEM_ADMIN__SYSTEMINFO] = $this->createMenuItem(
                self::ITEM_ADMIN__SYSTEMINFO,
                ['route' => 'ezplatform.systeminfo']
            );
        }

        if ($this->permissionResolver->hasAccess('section', 'view')) {
            $menuItems[self::ITEM_ADMIN__SECTIONS] = $this->createMenuItem(
                self::ITEM_ADMIN__SECTIONS,
                ['route' => 'ezplatform.section.list', 'extras' => [
                    'routes' => [
                        'update' => 'ezplatform.section.update',
                        'view' => 'ezplatform.section.view',
                        'create' => 'ezplatform.section.create',
                    ],
                ]]
            );
        }

        if ($this->permissionResolver->hasAccess('role', 'read')) {
            $menuItems[self::ITEM_ADMIN__ROLES] = $this->createMenuItem(
                self::ITEM_ADMIN__ROLES,
                ['route' => 'ezplatform.role.list', 'extras' => [
                    'routes' => [
                        'update' => 'ezplatform.role.update',
                        'view' => 'ezplatform.role.view',
                        'create' => 'ezplatform.role.create',
                        'policy_update' => 'ezplatform.policy.update',
                        'policy_list' => 'ezplatform.policy.list',
                        'policy_create' => 'ezplatform.policy.create',
                    ],
                ]]
            );
        }

        $menuItems[self::ITEM_ADMIN__STATE] = $this->createMenuItem(
            self::ITEM_ADMIN__STATE,
            ['route' => 'ezplatform.object_state.groups.list']
        );

        $menuItems[self::ITEM_ADMIN__LANGUAGES] = $this->createMenuItem(
            self::ITEM_ADMIN__LANGUAGES,
            ['route' => 'ezplatform.language.list', 'extras' => [
                'routes' => [
                    'edit' => 'ezplatform.language.edit',
                    'view' => 'ezplatform.language.view',
                    'create' => 'ezplatform.language.create',
                ],
            ]]
        );

        $menuItems[self::ITEM_ADMIN__CONTENT_TYPES] = $this->createMenuItem(
            self::ITEM_ADMIN__CONTENT_TYPES,
            ['route' => 'ezplatform.content_type_group.list', 'extras' => [
                'routes' => [
                    'update' => 'ezplatform.content_type_group.update',
                    'view' => 'ezplatform.content_type_group.view',
                    'create' => 'ezplatform.content_type_group.create',
                    'content_type_add' => 'ezplatform.content_type.add',
                    'content_type_view' => 'ezplatform.content_type.view',
                    'content_type_edit' => 'ezplatform.content_type.edit',
                    'content_type_update' => 'ezplatform.content_type.update',
                ],
            ]]
        );

        $rootUsersId = $this->configResolver->getParameter('location_ids.users');
        $usersItem = $this->factory->createLocationMenuItem(
            self::ITEM_ADMIN__USERS,
            $rootUsersId,
            ['label' => self::ITEM_ADMIN__USERS]
        );

        if (null !== $usersItem) {
            $menuItems[$usersItem->getName()] = $usersItem;
        }

        return $menuItems;
    }

    /**
     * @return ItemInterface|null
     */
    private function createLinkManagerMenuItem(): ?ItemInterface
    {
        if (!$this->permissionResolver->hasAccess('url', 'view')) {
            return null;
        }

        return $this->factory->createItem(
            self::ITEM_CONTENT__LINK_MANAGER,
            [
                'route' => 'ezplatform.link_manager.list',
                'extras' => [
                    'routes' => [
                        'edit' => 'ezplatform.link_manager.edit',
                        'view' => 'ezplatform.link_manager.view',
                    ],
                ],
            ]
        );
    }

    /**
     * @return array
     */
    public static function getTranslationMessages(): array
    {
        return [
            (new Message(self::ITEM_CONTENT, 'menu'))->setDesc('Content'),
            (new Message(self::ITEM_CONTENT__CONTENT_STRUCTURE, 'menu'))->setDesc('Content structure'),
            (new Message(self::ITEM_CONTENT__MEDIA, 'menu'))->setDesc('Media'),
            (new Message(self::ITEM_ADMIN, 'menu'))->setDesc('Admin'),
            (new Message(self::ITEM_ADMIN__SYSTEMINFO, 'menu'))->setDesc('System Information'),
            (new Message(self::ITEM_ADMIN__SECTIONS, 'menu'))->setDesc('Sections'),
            (new Message(self::ITEM_ADMIN__ROLES, 'menu'))->setDesc('Roles'),
            (new Message(self::ITEM_ADMIN__LANGUAGES, 'menu'))->setDesc('Languages'),
            (new Message(self::ITEM_ADMIN__CONTENT_TYPES, 'menu'))->setDesc('Content Types'),
            (new Message(self::ITEM_ADMIN__USERS, 'menu'))->setDesc('Users'),
            (new Message(self::ITEM_CONTENT__LINK_MANAGER, 'menu'))->setDesc('Link Manager'),
            (new Message(self::ITEM_ADMIN__STATE, 'menu'))->setDesc('State'),
        ];
    }
}
