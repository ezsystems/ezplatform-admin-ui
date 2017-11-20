<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Menu;

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

    /**
     * @param FactoryInterface $factory
     * @param EventDispatcherInterface $eventDispatcher
     * @param ConfigResolverInterface $configResolver
     */
    public function __construct(
        FactoryInterface $factory,
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
     * @throws InvalidArgumentException
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

        $menu[self::ITEM_CONTENT]->setChildren([
            self::ITEM_CONTENT__CONTENT_STRUCTURE => $this->createMenuItem(
                self::ITEM_CONTENT__CONTENT_STRUCTURE,
                [
                    'route' => '_ezpublishLocation',
                    'routeParameters' => [
                        'locationId' => $this->configResolver->getParameter('location_ids.content'),
                    ],
                ]
            ),
            self::ITEM_CONTENT__MEDIA => $this->createMenuItem(
                self::ITEM_CONTENT__MEDIA,
                [
                    'route' => '_ezpublishLocation',
                    'routeParameters' => [
                        'locationId' => $this->configResolver->getParameter('location_ids.media'),
                    ],
                ]
            ),
        ]);

        $menu[self::ITEM_ADMIN]->setChildren([
            self::ITEM_ADMIN__SYSTEMINFO => $this->createMenuItem(
                self::ITEM_ADMIN__SYSTEMINFO,
                ['route' => 'ezplatform.systeminfo']
            ),
            self::ITEM_ADMIN__SECTIONS => $this->createMenuItem(
                self::ITEM_ADMIN__SECTIONS,
                ['route' => 'ezplatform.section.list']
            ),
            self::ITEM_ADMIN__ROLES => $this->createMenuItem(
                self::ITEM_ADMIN__ROLES,
                ['route' => 'ezplatform.role.list']
            ),
            self::ITEM_ADMIN__LANGUAGES => $this->createMenuItem(
                self::ITEM_ADMIN__LANGUAGES,
                ['route' => 'ezplatform.language.list']
            ),
            self::ITEM_ADMIN__CONTENT_TYPES => $this->createMenuItem(
                    self::ITEM_ADMIN__CONTENT_TYPES,
                    ['route' => 'ezplatform.content_type_group.list']
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
