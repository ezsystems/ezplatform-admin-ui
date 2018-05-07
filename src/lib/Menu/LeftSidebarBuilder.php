<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Menu;

use eZ\Publish\Core\MVC\ConfigResolverInterface;
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
    /** @var \eZ\Publish\Core\MVC\ConfigResolverInterface */
    private $configResolver;

    /** @var \EzSystems\EzPlatformAdminUiBundle\Templating\Twig\UniversalDiscoveryExtension */
    private $udwExtension;

    /* Menu items */
    const ITEM__SEARCH = 'sidebar_left__search';
    const ITEM__BROWSE = 'sidebar_left__browse';
    const ITEM__TRASH = 'sidebar_left__trash';

    /**
     * @param \EzSystems\EzPlatformAdminUi\Menu\MenuItemFactory $factory
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher
     * @param \eZ\Publish\Core\MVC\ConfigResolverInterface $configResolver
     * @param \EzSystems\EzPlatformAdminUiBundle\Templating\Twig\UniversalDiscoveryExtension $udwExtension
     */
    public function __construct(
        MenuItemFactory $factory,
        EventDispatcherInterface $eventDispatcher,
        ConfigResolverInterface $configResolver,
        UniversalDiscoveryExtension $udwExtension
    ) {
        parent::__construct($factory, $eventDispatcher);

        $this->configResolver = $configResolver;
        $this->udwExtension = $udwExtension;
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
     */
    public function createStructure(array $options): ItemInterface
    {
        $menu = $this->factory->createItem('root');

        $menu->setChildren([
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
                        'class' => 'btn--udw-browse',
                        'data-udw-config' => $this->udwExtension->renderUniversalDiscoveryWidgetConfig('single'),
                        'data-starting-location-id' => $this->configResolver->getParameter(
                            'universal_discovery_widget_module.default_location_id'
                        ),
                    ],
                ]
            ),
            self::ITEM__TRASH => $this->createMenuItem(
                self::ITEM__TRASH,
                [
                    'route' => 'ezplatform.trash.list',
                    'extras' => ['icon' => 'trash'],
                ]
            ),
        ]);

        return $menu;
    }

    /**
     * @return JMS\TranslationBundle\Model\Message[]
     */
    public static function getTranslationMessages(): array
    {
        return [
            (new Message(self::ITEM__SEARCH, 'menu'))->setDesc('Search'),
            (new Message(self::ITEM__BROWSE, 'menu'))->setDesc('Browse'),
            (new Message(self::ITEM__TRASH, 'menu'))->setDesc('Trash'),
        ];
    }
}
