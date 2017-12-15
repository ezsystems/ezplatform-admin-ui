<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Menu;

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

    /** @var ConfigResolverInterface */
    private $configResolver;

    /**
     * @param MenuItemFactory $factory
     * @param EventDispatcherInterface $eventDispatcher
     * @param ConfigResolverInterface $configResolver
     */
    public function __construct(
        MenuItemFactory $factory,
        EventDispatcherInterface $eventDispatcher,
        ConfigResolverInterface $configResolver
    ) {
        parent::__construct($factory, $eventDispatcher);

        $this->configResolver = $configResolver;
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

        $menu->setChildren([
            self::ITEM_CONTENT => $this->factory->createItem(
                self::ITEM_CONTENT,
                []
            ),
            self::ITEM_ADMIN => $this->factory->createItem(
                self::ITEM_ADMIN,
                []
            ),
        ]);

        $menu[self::ITEM_CONTENT]->setChildren($this->getContentMenuItems());
        $menu[self::ITEM_ADMIN]->setChildren($this->getAdminMenuItems());

        return $menu;
    }

    /**
     * @return array
     */
    private function getContentMenuItems(): array
    {
        $menuItems = [];

        $rootContentId = $this->configResolver->getParameter('content.tree_root.location_id');
        $rootMediaId = $this->configResolver->getParameter('location_ids.media');

        $contentStructureItem = $this->factory->createLocationMenuItem(
            self::ITEM_CONTENT__CONTENT_STRUCTURE,
            $rootContentId,
            ['label' => self::ITEM_CONTENT__CONTENT_STRUCTURE]
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

        $menuItems[self::ITEM_CONTENT__LINK_MANAGER] = $this->factory->createItem(
            self::ITEM_CONTENT__LINK_MANAGER,
            [
                'route' => 'ezplatform.link_manager.list',
            ]
        );

        return $menuItems;
    }

    /**
     * @return array
     */
    private function getAdminMenuItems(): array
    {
        $menuItems = [
            self::ITEM_ADMIN__SYSTEMINFO => $this->createMenuItem(
                self::ITEM_ADMIN__SYSTEMINFO,
                ['route' => 'ezplatform.systeminfo']
            ),
            self::ITEM_ADMIN__SECTIONS => $this->createMenuItem(
                self::ITEM_ADMIN__SECTIONS,
                ['route' => 'ezplatform.section.list', 'extras' => [
                    'routes' => [
                        'update' => 'ezplatform.section.update',
                        'view' => 'ezplatform.section.view',
                        'create' => 'ezplatform.section.create',
                    ],
                ]]
            ),
            self::ITEM_ADMIN__ROLES => $this->createMenuItem(
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
            ),
            self::ITEM_ADMIN__LANGUAGES => $this->createMenuItem(
                self::ITEM_ADMIN__LANGUAGES,
                ['route' => 'ezplatform.language.list', 'extras' => [
                    'routes' => [
                        'edit' => 'ezplatform.language.edit',
                        'view' => 'ezplatform.language.view',
                        'create' => 'ezplatform.language.create',
                    ],
                ]]
            ),
            self::ITEM_ADMIN__CONTENT_TYPES => $this->createMenuItem(
                self::ITEM_ADMIN__CONTENT_TYPES,
                ['route' => 'ezplatform.content_type_group.list', 'extras' => [
                    'routes' => [
                        'update' => 'ezplatform.content_type_group.update',
                        'view' => 'ezplatform.content_type_group.view',
                        'create' => 'ezplatform.content_type_group.create',
                        'content_type_add' => 'ezplatform.content_type.add',
                        'content_type_view' => 'ezplatform.content_type.view',
                        'content_type_edit' => 'ezplatform.content_type.edit',
                    ],
                ]]
            ),
        ];

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
        ];
    }
}
