<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Menu;

use eZ\Publish\Core\MVC\ConfigResolverInterface;
use EzSystems\EzPlatformAdminUi\Menu\Event\ConfigureMenuEvent;
use InvalidArgumentException;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class MainMenuBuilder extends AbstractBuilder implements TranslationContainerInterface
{
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

        $menu->addChild($this->createMenuItem('main__content', ['extras' => ['icon' => 'content-list']]));
        $menu->addChild($this->createMenuItem('main__admin', ['extras' => ['icon' => 'panel']]));

        $menu['main__content']->addChild(
            $this->createMenuItem('main__content__content_structure', [
                'route' => '_ezpublishLocation',
                'routeParameters' => [
                    'locationId' => $this->configResolver->getParameter('location_ids.content'),
                ],
            ])
        );
        $menu['main__content']->addChild(
            $this->createMenuItem('main__content__media', [
                'route' => '_ezpublishLocation',
                'routeParameters' => [
                    'locationId' => $this->configResolver->getParameter('location_ids.media'),
                ],
            ])
        );

        $menu['main__admin']->addChild(
            $this->createMenuItem('main__admin__systeminfo', ['route' => 'ezplatform.systeminfo'])
        );
        $menu['main__admin']->addChild(
            $this->createMenuItem('main__admin__sections', ['route' => 'ezplatform.section.list'])
        );
        $menu['main__admin']->addChild(
            $this->createMenuItem('main__admin__roles', ['route' => 'ezplatform.role.list'])
        );
        $menu['main__admin']->addChild(
            $this->createMenuItem('main__admin__languages', ['route' => 'ezplatform.language.list'])
        );
        $menu['main__admin']->addChild(
            $this->createMenuItem('main__admin__content_types', ['route' => 'ezplatform.content_type_group.list'])
        );
        $menu['main__admin']->addChild(
            $this->createMenuItem('main__admin__users', [
                'route' => '_ezpublishLocation',
                'routeParameters' => [
                    'locationId' => $this->configResolver->getParameter('location_ids.users'),
                ],
            ])
        );

        return $menu;
    }

    /**
     * @return array
     */
    public static function getTranslationMessages(): array
    {
        return [
            (new Message('main__content', 'menu'))->setDesc('Content'),
            (new Message('main__content__content_structure', 'menu'))->setDesc('Content structure'),
            (new Message('main__content__media', 'menu'))->setDesc('Media'),
            (new Message('main__admin', 'menu'))->setDesc('Admin'),
            (new Message('main__admin__systeminfo', 'menu'))->setDesc('System Information'),
            (new Message('main__admin__sections', 'menu'))->setDesc('Sections'),
            (new Message('main__admin__roles', 'menu'))->setDesc('Roles'),
            (new Message('main__admin__languages', 'menu'))->setDesc('Languages'),
            (new Message('main__admin__content_types', 'menu'))->setDesc('Content Types'),
            (new Message('main__admin__users', 'menu'))->setDesc('Users'),
        ];
    }
}
