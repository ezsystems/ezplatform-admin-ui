<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Menu;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\PermissionResolver;
use eZ\Publish\Core\MVC\ConfigResolverInterface;
use EzSystems\EzPlatformAdminUi\Menu\Event\ConfigureMenuEvent;
use InvalidArgumentException;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use Knp\Menu\FactoryInterface;
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

    /** @var PermissionResolver */
    private $permissionResolver;

    /** @var LocationService */
    private $locationService;

    /** @var ContentService */
    private $contentService;

    /**
     * @param FactoryInterface $factory
     * @param EventDispatcherInterface $eventDispatcher
     * @param ConfigResolverInterface $configResolver
     * @param PermissionResolver $permissionResolver
     * @param LocationService $locationService
     * @param ContentService $contentService
     */
    public function __construct(
        FactoryInterface $factory,
        EventDispatcherInterface $eventDispatcher,
        ConfigResolverInterface $configResolver,
        PermissionResolver $permissionResolver,
        LocationService $locationService,
        ContentService $contentService
    ) {
        parent::__construct($factory, $eventDispatcher);

        $this->configResolver = $configResolver;
        $this->permissionResolver = $permissionResolver;
        $this->locationService = $locationService;
        $this->contentService = $contentService;
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
        $menu = $this->factory->createItem('root');

        $menu->setChildren([
            self::ITEM_CONTENT => $this->createMenuItem(
                self::ITEM_CONTENT,
                []
            ),
            self::ITEM_ADMIN => $this->createMenuItem(
                self::ITEM_ADMIN,
                []
            ),
        ]);

        // Is user has access to Content location ?
        $rootContentId = $this->configResolver->getParameter('content.tree_root.location_id');

        try {
            $this->permissionResolver->canUser(
                'content',
                'read',
                $this->locationService->loadLocation($rootContentId)->contentInfo
            );

            $menu[self::ITEM_CONTENT]->addChild(
                self::ITEM_CONTENT__CONTENT_STRUCTURE,
                [
                    'label' => self::ITEM_CONTENT__CONTENT_STRUCTURE,
                    'route' => '_ezpublishLocation',
                    'routeParameters' => [
                        'locationId' => $rootContentId,
                    ],
                ]
            )->setExtra('translation_domain', 'menu');
        } catch (\Exception $e) {}

        try {
            // Is User has access to Media Location ?
            $rootMediaId = $this->configResolver->getParameter('location_ids.media');
            $this->permissionResolver->canUser(
            'content',
            'read',
                $this->locationService->loadLocation($rootMediaId)->contentInfo
            );

            $menu[self::ITEM_CONTENT]->addChild(
                self::ITEM_CONTENT__MEDIA,
                [
                    'route' => '_ezpublishLocation',
                    'routeParameters' => [
                        'locationId' => $rootMediaId,
                    ],
                ]

            )->setExtra('translation_domain', 'menu');
        } catch (\Exception $e) {}

        $menu[self::ITEM_ADMIN]->setChildren([
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
            self::ITEM_ADMIN__USERS => $this->createMenuItem(
                self::ITEM_ADMIN__USERS,
                [
                    'route' => '_ezpublishLocation',
                    'routeParameters' => [
                        'locationId' => $this->configResolver->getParameter('location_ids.users'),
                    ],
                ]
            ),
        ]);

        return $menu;
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
