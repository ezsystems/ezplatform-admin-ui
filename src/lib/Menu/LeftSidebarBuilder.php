<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Menu;

use eZ\Publish\API\Repository\PermissionResolver;
use EzSystems\EzPlatformAdminUi\UniversalDiscovery\ConfigResolver;
use EzSystems\EzPlatformAdminUi\Menu\Event\ConfigureMenuEvent;
use EzSystems\EzPlatformAdminUiBundle\Templating\Twig\UniversalDiscoveryExtension;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * KnpMenuBundle Menu Builder service implementation for AdminUI left sidebar menu.
 *
 * @see https://symfony.com/doc/current/bundles/KnpMenuBundle/menu_builder_service.html
 */
class LeftSidebarBuilder extends AbstractBuilder implements TranslationContainerInterface
{
    /* Menu items */
    const ITEM__SEARCH = 'sidebar_left__search';
    const ITEM__BROWSE = 'sidebar_left__browse';
    const ITEM__BOOKMARK = 'sidebar_left__bookmark';
    const ITEM__TRASH = 'sidebar_left__trash';
    const ITEM__TREE = 'sidebar_left__tree';

    /** @var \EzSystems\EzPlatformAdminUi\UniversalDiscovery\ConfigResolver */
    private $configResolver;

    /** @var \EzSystems\EzPlatformAdminUiBundle\Templating\Twig\UniversalDiscoveryExtension */
    private $udwExtension;

    /** @var \eZ\Publish\API\Repository\PermissionResolver */
    private $permissionResolver;

    /**
     * @param \EzSystems\EzPlatformAdminUi\Menu\MenuItemFactory $factory
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher
     * @param \EzSystems\EzPlatformAdminUi\UniversalDiscovery\ConfigResolver $configResolver
     * @param \EzSystems\EzPlatformAdminUiBundle\Templating\Twig\UniversalDiscoveryExtension $udwExtension
     * @param \eZ\Publish\API\Repository\PermissionResolver $permissionResolver
     */
    public function __construct(
        MenuItemFactory $factory,
        EventDispatcherInterface $eventDispatcher,
        ConfigResolver $configResolver,
        UniversalDiscoveryExtension $udwExtension,
        PermissionResolver $permissionResolver
    ) {
        parent::__construct($factory, $eventDispatcher);

        $this->configResolver = $configResolver;
        $this->udwExtension = $udwExtension;
        $this->permissionResolver = $permissionResolver;
    }

    /**
     * @return string
     */
    protected function getConfigureEventName(): string
    {
        return ConfigureMenuEvent::CONTENT_SIDEBAR_LEFT;
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
        $menu = $this->factory->createItem('root');

        $menuItems = [
            self::ITEM__SEARCH => $this->createMenuItem(
                self::ITEM__SEARCH,
                [
                    'route' => 'ezplatform.search',
                    'extras' => ['icon' => 'search'],
                ]
            ),
            self::ITEM__BROWSE => $this->createMenuItem(
                self::ITEM__BROWSE,
                [
                    'extras' => ['icon' => 'browse'],
                    'attributes' => [
                        'type' => 'button',
                        'class' => 'btn--udw-browse',
                        'data-udw-config' => $this->udwExtension->renderUniversalDiscoveryWidgetConfig('single', [
                            'type' => 'content_create',
                        ]),
                        'data-starting-location-id' => $this->configResolver->getConfig('default')['starting_location_id'],
                    ],
                ]
            ),
            'sidebar_left__browse2' => $this->createMenuItem(
                'sidebar_left__browse2',
                [
                    'extras' => ['icon' => 'browse'],
                    'attributes' => [
                        'type' => 'button',
                        'class' => 'btn--new-udw-browse',
                        'data-udw-config' => $this->udwExtension->renderUniversalDiscoveryWidgetConfig('single', [
                            'type' => 'content_create',
                        ]),
                        'data-starting-location-id' => $this->configResolver->getParameter(
                            'universal_discovery_widget_module.default_location_id'
                        ),
                    ],
                ]
            ),
            self::ITEM__TREE => $this->createMenuItem(
                self::ITEM__TREE,
                [
                    'extras' => ['icon' => 'content-tree'],
                    'attributes' => [
                        'type' => 'button',
                        'class' => 'ez-btn ez-btn--toggle-content-tree',
                    ],
                ]
            ),
            self::ITEM__BOOKMARK => $this->createMenuItem(
                self::ITEM__BOOKMARK,
                [
                    'route' => 'ezplatform.bookmark.list',
                    'extras' => ['icon' => 'bookmark-manager'],
                ]
            ),
        ];

        if ($this->permissionResolver->hasAccess('content', 'restore')) {
            $menuItems[self::ITEM__TRASH] = $this->createMenuItem(
                self::ITEM__TRASH,
                [
                    'route' => 'ezplatform.trash.list',
                    'extras' => ['icon' => 'trash'],
                ]
            );
        }

        $menu->setChildren($menuItems);

        return $menu;
    }

    /**
     * @return \JMS\TranslationBundle\Model\Message[]
     */
    public static function getTranslationMessages(): array
    {
        return [
            (new Message(self::ITEM__SEARCH, 'menu'))->setDesc('Search'),
            (new Message(self::ITEM__BROWSE, 'menu'))->setDesc('Browse'),
            (new Message(self::sidebar_left__browse2, 'menu'))->setDesc('new browse'),
            (new Message(self::ITEM__TREE, 'menu'))->setDesc('Content Tree'),
            (new Message(self::ITEM__TRASH, 'menu'))->setDesc('Trash'),
            (new Message(self::ITEM__BOOKMARK, 'menu'))->setDesc('Bookmarks'),
        ];
    }
}
