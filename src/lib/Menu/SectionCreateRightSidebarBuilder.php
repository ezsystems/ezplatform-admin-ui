<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Menu;

use eZ\Publish\API\Repository\Exceptions as ApiExceptions;
use EzSystems\EzPlatformAdminUi\Menu\Event\ConfigureMenuEvent;
use InvalidArgumentException;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use Knp\Menu\ItemInterface;

/**
 * KnpMenuBundle Menu Builder service implementation for AdminUI Section Edit contextual sidebar menu.
 *
 * @see https://symfony.com/doc/current/bundles/KnpMenuBundle/menu_builder_service.html
 */
class SectionCreateRightSidebarBuilder extends AbstractBuilder implements TranslationContainerInterface
{
    /* Menu items */
    const ITEM__CREATE = 'section_create__sidebar_right__create';
    const ITEM__CANCEL = 'section_create__sidebar_right__cancel';

    /**
     * @return string
     */
    protected function getConfigureEventName(): string
    {
        return ConfigureMenuEvent::SECTION_CREATE_SIDEBAR_RIGHT;
    }

    /**
     * @param array $options
     *
     * @return ItemInterface
     *
     * @throws ApiExceptions\InvalidArgumentException
     * @throws ApiExceptions\BadStateException
     * @throws InvalidArgumentException
     */
    public function createStructure(array $options): ItemInterface
    {
        /** @var ItemInterface|ItemInterface[] $menu */
        $menu = $this->factory->createItem('root');

        $menu->setChildren([
            self::ITEM__CREATE => $this->createMenuItem(
                self::ITEM__CREATE,
                [
                    'attributes' => [
                        'class' => 'btn--trigger',
                        'data-click' => '#section_create_create',
                    ],
                    'extras' => ['icon' => 'save'],
                ]
            ),
            self::ITEM__CANCEL => $this->createMenuItem(
                self::ITEM__CANCEL,
                [
                    'route' => 'ezplatform.section.list',
                    'extras' => ['icon' => 'circle-close'],
                ]
            ),
        ]);

        return $menu;
    }

    /**
     * @return Message[]
     */
    public static function getTranslationMessages(): array
    {
        return [
            (new Message(self::ITEM__CREATE, 'menu'))->setDesc('Create'),
            (new Message(self::ITEM__CANCEL, 'menu'))->setDesc('Discard changes'),
        ];
    }
}
