<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Menu;

use EzSystems\EzPlatformAdminUi\Menu\Event\ConfigureMenuEvent;
use InvalidArgumentException;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use Knp\Menu\ItemInterface;

class LeftSidebarBuilder extends AbstractBuilder implements TranslationContainerInterface
{
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
     * @return ItemInterface
     * @throws InvalidArgumentException
     */
    public function createStructure(array $options): ItemInterface
    {
        $menu = $this->factory->createItem('root');

        $menu->addChild(
            $this->createMenuItem('sidebar_left__search', [
                'extras' => ['icon' => 'search'],
                'attributes' => [
                    'disabled' => 'disabled',
                ],
            ])
        );
        $menu->addChild(
            $this->createMenuItem('sidebar_left__browse', [
                'extras' => ['icon' => 'browse'],
                'attributes' => [
                    'class' => 'btn--udw-browse',
                    'data-starting-location-id' => 1,
                ],
            ])
        );
        $menu->addChild($this->createMenuItem('sidebar_left__trash', [
            'route' => 'ezplatform.trash.list',
            'extras' => ['icon' => 'trash'],
        ]));

        return $menu;
    }

    /**
     * @return array
     */
    public static function getTranslationMessages(): array
    {
        return [
            (new Message('sidebar_left__search', 'menu'))->setDesc('Search'),
            (new Message('sidebar_left__browse', 'menu'))->setDesc('Browse'),
            (new Message('sidebar_left__trash', 'menu'))->setDesc('Trash'),
        ];
    }
}
