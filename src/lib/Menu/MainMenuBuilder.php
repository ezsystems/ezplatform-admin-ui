<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
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
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * KnpMenuBundle Menu Builder service implementation for AdminUI top menu.
 *
 * @see https://symfony.com/doc/current/bundles/KnpMenuBundle/menu_builder_service.html
 */
class MainMenuBuilder extends AbstractBuilder implements TranslationContainerInterface
{
    /* Main Menu / Dashboard */
    const ITEM_DASHBOARD = 'main__dashboard';

    /* Main Menu / Content */
    const ITEM_CONTENT = 'main__content';
    const ITEM_CONTENT__CONTENT_STRUCTURE = 'main__content__content_structure';
    const ITEM_CONTENT__MEDIA = 'main__content__media';

    /* Main Menu / Admin */
    const ITEM_ADMIN__SECTIONS = 'main__admin__sections';
    const ITEM_ADMIN__ROLES = 'main__admin__roles';
    const ITEM_ADMIN__LANGUAGES = 'main__admin__languages';
    const ITEM_ADMIN__CONTENT_TYPES = 'main__admin__content_types';
    const ITEM_ADMIN__USERS = 'main__admin__users';
    const ITEM_ADMIN__OBJECT_STATES = 'main__admin__object_states';
    const ITEM_ADMIN__URL_MANAGEMENT = 'main__admin__url_management';

    /* Main Menu / Bottom items */
    const ITEM_ADMIN = 'main__admin';
    const ITEM_BOOKMARKS = 'main__bookmarks';
    const ITEM_TRASH = 'main__trash';

    public const ITEM_ADMIN_OPTIONS = [
        self::ITEM_ADMIN__SECTIONS => [
            'route' => 'ezplatform.section.list',
            'extras' => [
                'routes' => [
                    'update' => 'ezplatform.section.update',
                    'view' => 'ezplatform.section.view',
                    'create' => 'ezplatform.section.create',
                ],
                'orderNumber' => 20,
            ],
        ],
        self::ITEM_ADMIN__ROLES => [
            'route' => 'ezplatform.role.list',
            'extras' => [
                'routes' => [
                    'update' => 'ezplatform.role.update',
                    'view' => 'ezplatform.role.view',
                    'create' => 'ezplatform.role.create',
                    'policy_update' => 'ezplatform.policy.update',
                    'policy_list' => 'ezplatform.policy.list',
                    'policy_create' => 'ezplatform.policy.create',
                    'policy_create_with_limitation' => 'ezplatform.policy.create_with_limitation',
                ],
                'orderNumber' => 30,
            ],
        ],
        self::ITEM_ADMIN__LANGUAGES => [
            'route' => 'ezplatform.language.list',
            'extras' => [
                'routes' => [
                    'edit' => 'ezplatform.language.edit',
                    'view' => 'ezplatform.language.view',
                    'create' => 'ezplatform.language.create',
                ],
                'orderNumber' => 40,
            ],
        ],
        self::ITEM_ADMIN__CONTENT_TYPES => [
            'route' => 'ezplatform.content_type_group.list',
            'extras' => [
                'routes' => [
                    'update' => 'ezplatform.content_type_group.update',
                    'view' => 'ezplatform.content_type_group.view',
                    'create' => 'ezplatform.content_type_group.create',
                    'content_type_add' => 'ezplatform.content_type.add',
                    'content_type_view' => 'ezplatform.content_type.view',
                    'content_type_edit' => 'ezplatform.content_type.edit',
                    'content_type_update' => 'ezplatform.content_type.update',
                ],
                'orderNumber' => 50,
            ],
        ],
        self::ITEM_ADMIN__OBJECT_STATES => [
            'route' => 'ezplatform.object_state.groups.list',
            'extras' => [
                'routes' => [
                    'group_list' => 'ezplatform.object_state.groups.list',
                    'group_create' => 'ezplatform.object_state.group.add',
                    'group_edit' => 'ezplatform.object_state.group.update',
                    'group_view' => 'ezplatform.object_state.group.view',
                    'state_create' => 'ezplatform.object_state.state.add',
                    'state_view' => 'ezplatform.object_state.state.view',
                    'state_edit' => 'ezplatform.object_state.state.update',
                ],
                'orderNumber' => 60,
            ],
        ],
        self::ITEM_ADMIN__URL_MANAGEMENT => [
            'route' => 'ezplatform.url_management',
            'extras' => [
                'routes' => [
                    'link_manager_edit' => 'ezplatform.link_manager.edit',
                    'link_manager_view' => 'ezplatform.link_manager.view',
                    'url_wildcard_edit' => 'ezplatform.url_wildcard.update',
                ],
                'orderNumber' => 80,
            ],
        ],
    ];

    /** @var \eZ\Publish\Core\MVC\ConfigResolverInterface */
    private $configResolver;

    /** @var \eZ\Publish\API\Repository\PermissionResolver */
    private $permissionResolver;

    /** @var \Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface */
    private $tokenStorage;

    /**
     * @param \EzSystems\EzPlatformAdminUi\Menu\MenuItemFactory $factory
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher
     * @param \eZ\Publish\Core\MVC\ConfigResolverInterface $configResolver
     * @param \eZ\Publish\API\Repository\PermissionResolver $permissionResolver
     */
    public function __construct(
        MenuItemFactory $factory,
        EventDispatcherInterface $eventDispatcher,
        ConfigResolverInterface $configResolver,
        PermissionResolver $permissionResolver,
        TokenStorageInterface $tokenStorage
    ) {
        parent::__construct($factory, $eventDispatcher);

        $this->configResolver = $configResolver;
        $this->permissionResolver = $permissionResolver;
        $this->tokenStorage = $tokenStorage;
    }

    protected function getConfigureEventName(): string
    {
        return ConfigureMenuEvent::MAIN_MENU;
    }

    /**
     * @param array $options
     *
     * @return \Knp\Menu\ItemInterface
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     */
    public function createStructure(array $options): ItemInterface
    {
        $token = $this->tokenStorage->getToken();

        /** @var \Knp\Menu\ItemInterface|\Knp\Menu\ItemInterface[] $menu */
        $menu = $this->factory->createItem('root');

        $menu->addChild($this->factory->createItem(self::ITEM_DASHBOARD, [
                'route' => 'ezplatform.dashboard',
                'attributes' => [
                    'data-tooltip-placement' => 'right',
                    'data-tooltip-extra-class' => 'ibexa-tooltip--info-neon',
                ],
                'extras' => [
                    'icon' => 'dashboard-clean',
                    'orderNumber' => 20,
                ],
            ]
        ));

        $menu->addChild($this->factory->createItem(self::ITEM_CONTENT, [
                'attributes' => [
                    'data-tooltip-placement' => 'right',
                    'data-tooltip-extra-class' => 'ibexa-tooltip--info-neon',
                ],
                'extras' => [
                    'icon' => 'hierarchy',
                    'orderNumber' => 40,
                ],
            ]
        ));

        $menu->addChild($this->factory->createItem(self::ITEM_ADMIN, [
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
            ]
        ));

        if (null !== $token && is_object($token->getUser())) {
            $menu->addChild($this->factory->createItem(self::ITEM_BOOKMARKS, [
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
                ]
            ));
        }

        $menu->addChild($this->factory->createItem(self::ITEM_TRASH, [
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
            ]
        ));

        $contentMenuItems = $this->getContentMenuItems();
        $adminMenuItems = $this->getAdminMenuItems();

        $menu[self::ITEM_CONTENT]->setChildren($contentMenuItems);
        $menu[self::ITEM_ADMIN]->setChildren($adminMenuItems);

        return $menu;
    }

    /**
     * @return array
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
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
            ]
        );
        $mediaItem = $this->factory->createLocationMenuItem(
            self::ITEM_CONTENT__MEDIA,
            $rootMediaId,
            ['label' => self::ITEM_CONTENT__MEDIA]
        );

        if (null !== $contentStructureItem) {
            $menuItems[$contentStructureItem->getName()] = $contentStructureItem;
        }

        if (null !== $mediaItem) {
            $menuItems[$mediaItem->getName()] = $mediaItem;
        }

        return $menuItems;
    }

    /**
     * @return array
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     */
    private function getAdminMenuItems(): array
    {
        $menuItems = [];

        if ($this->permissionResolver->hasAccess('section', 'view') !== false) {
            $menuItems[self::ITEM_ADMIN__SECTIONS] = $this->createMenuItem(
                self::ITEM_ADMIN__SECTIONS,
                self::ITEM_ADMIN_OPTIONS[self::ITEM_ADMIN__SECTIONS]
            );
        }

        if ($this->permissionResolver->hasAccess('role', 'read')) {
            $menuItems[self::ITEM_ADMIN__ROLES] = $this->createMenuItem(
                self::ITEM_ADMIN__ROLES,
                self::ITEM_ADMIN_OPTIONS[self::ITEM_ADMIN__ROLES]
            );
        }
        if ($this->permissionResolver->hasAccess('setup', 'administrate')) {
            $menuItems[self::ITEM_ADMIN__LANGUAGES] = $this->createMenuItem(
                self::ITEM_ADMIN__LANGUAGES,
                self::ITEM_ADMIN_OPTIONS[self::ITEM_ADMIN__LANGUAGES]
            );
        }

        $menuItems[self::ITEM_ADMIN__CONTENT_TYPES] = $this->createMenuItem(
            self::ITEM_ADMIN__CONTENT_TYPES,
            self::ITEM_ADMIN_OPTIONS[self::ITEM_ADMIN__CONTENT_TYPES]
        );

        $rootUsersId = $this->configResolver->getParameter('location_ids.users');
        $usersItem = $this->factory->createLocationMenuItem(
            self::ITEM_ADMIN__USERS,
            $rootUsersId,
            [
                'label' => self::ITEM_ADMIN__USERS,
                'extras' => [
                    'orderNumber' => 60,
                ],
            ]
        );

        if (null !== $usersItem) {
            $menuItems[$usersItem->getName()] = $usersItem;
        }

        if ($this->permissionResolver->hasAccess('state', 'administrate')) {
            $menuItems[self::ITEM_ADMIN__OBJECT_STATES] = $this->createMenuItem(
                self::ITEM_ADMIN__OBJECT_STATES,
                self::ITEM_ADMIN_OPTIONS[self::ITEM_ADMIN__OBJECT_STATES]
            );
        }

        $menuItems[self::ITEM_ADMIN__URL_MANAGEMENT] = $this->createMenuItem(
            self::ITEM_ADMIN__URL_MANAGEMENT,
            self::ITEM_ADMIN_OPTIONS[self::ITEM_ADMIN__URL_MANAGEMENT]
        );

        return $menuItems;
    }

    /**
     * @return array
     */
    public static function getTranslationMessages(): array
    {
        return [
            (new Message(self::ITEM_DASHBOARD, 'menu'))->setDesc('Dashboard'),
            (new Message(self::ITEM_BOOKMARKS, 'menu'))->setDesc('Bookmarks'),
            (new Message(self::ITEM_TRASH, 'menu'))->setDesc('Trash'),
            (new Message(self::ITEM_CONTENT, 'menu'))->setDesc('Content'),
            (new Message(self::ITEM_CONTENT__CONTENT_STRUCTURE, 'menu'))->setDesc('Content structure'),
            (new Message(self::ITEM_CONTENT__MEDIA, 'menu'))->setDesc('Media'),
            (new Message(self::ITEM_ADMIN, 'menu'))->setDesc('Admin'),
            (new Message(self::ITEM_ADMIN__SECTIONS, 'menu'))->setDesc('Sections'),
            (new Message(self::ITEM_ADMIN__ROLES, 'menu'))->setDesc('Roles'),
            (new Message(self::ITEM_ADMIN__LANGUAGES, 'menu'))->setDesc('Languages'),
            (new Message(self::ITEM_ADMIN__CONTENT_TYPES, 'menu'))->setDesc('Content Types'),
            (new Message(self::ITEM_ADMIN__USERS, 'menu'))->setDesc('Users'),
            (new Message(self::ITEM_ADMIN__OBJECT_STATES, 'menu'))->setDesc('Object States'),
            (new Message(self::ITEM_ADMIN__URL_MANAGEMENT, 'menu'))->setDesc('URL Management'),
        ];
    }
}
