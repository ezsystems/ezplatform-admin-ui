<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Menu;

use eZ\Publish\API\Repository\Values\Content\Location;
use EzSystems\EzPlatformAdminUi\Menu\Event\ConfigureMenuEvent;
use InvalidArgumentException;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use Knp\Menu\ItemInterface;

/**
 * KnpMenuBundle Menu Builder service implementation for AdminUI Location View contextual sidebar menu.
 *
 * @see https://symfony.com/doc/current/bundles/KnpMenuBundle/menu_builder_service.html
 */
class ContentRightSidebarBuilder extends AbstractBuilder implements TranslationContainerInterface
{
    /* Menu items */
    const ITEM__SEND_TO_TRASH = 'content__sidebar_right__send_to_trash';
    const ITEM__COPY = 'content__sidebar_right__copy';
    const ITEM__MOVE = 'content__sidebar_right__move';

    /**
     * @return string
     */
    protected function getConfigureEventName(): string
    {
        return ConfigureMenuEvent::CONTENT_SIDEBAR_RIGHT;
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
        /** @var Location $location */
        $location = $options['location'];
        /** @var ItemInterface|ItemInterface[] $menu */
        $menu = $this->factory->createItem('root');

        $menu->setChildren([
            self::ITEM__SEND_TO_TRASH => $this->createMenuItem(
                self::ITEM__SEND_TO_TRASH,
                [
                    'extras' => ['icon' => 'trash-send'],
                    'attributes' => [
                        'data-toggle' => 'modal',
                        'data-target' => '#trash-location-modal',
                    ],
                ]
            ),
            self::ITEM__COPY => $this->createMenuItem(
                self::ITEM__COPY,
                [
                    'extras' => ['icon' => 'copy'],
                    'attributes' => [
                        'class' => 'btn--udw-copy',
                        'data-root-location' => 1,
                    ],
                ]
            ),
            self::ITEM__MOVE => $this->createMenuItem(
                self::ITEM__MOVE,
                [
                    'extras' => ['icon' => 'move'],
                    'attributes' => [
                        'class' => 'btn--udw-move',
                        'data-root-location' => 1,
                    ],
                ]
            ),
        ]);

        if (1 === $location->depth) {
            $menu[self::ITEM__SEND_TO_TRASH]->setAttribute('disabled', 'disabled');
            $menu[self::ITEM__MOVE]->setAttribute('disabled', 'disabled');
        }

        return $menu;
    }

    /**
     * @return Message[]
     */
    public static function getTranslationMessages(): array
    {
        return [
            (new Message(self::ITEM__SEND_TO_TRASH, 'menu'))->setDesc('Send to Trash'),
            (new Message(self::ITEM__COPY, 'menu'))->setDesc('Copy'),
            (new Message(self::ITEM__MOVE, 'menu'))->setDesc('Move'),
        ];
    }
}
