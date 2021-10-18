<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Menu\Admin\ObjectState;

use eZ\Publish\API\Repository\Exceptions as ApiExceptions;
use EzSystems\EzPlatformAdminUi\Menu\AbstractBuilder;
use EzSystems\EzPlatformAdminUi\Menu\Event\ConfigureMenuEvent;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use Knp\Menu\ItemInterface;

/**
 * KnpMenuBundle Menu Builder service implementation for AdminUI Section Edit contextual sidebar menu.
 *
 * @see https://symfony.com/doc/current/bundles/KnpMenuBundle/menu_builder_service.html
 */
class ObjectStateCreateRightSidebarBuilder extends AbstractBuilder implements TranslationContainerInterface
{
    /* Menu items */
    const ITEM__CREATE = 'object_state_create__sidebar_right__create';
    const ITEM__CANCEL = 'object_state_create__sidebar_right__cancel';

    /**
     * @return string
     */
    protected function getConfigureEventName(): string
    {
        return ConfigureMenuEvent::OBJECT_STATE_CREATE_SIDEBAR_RIGHT;
    }

    /**
     * @param array $options
     *
     * @return \Knp\Menu\ItemInterface
     *
     * @throws \InvalidArgumentException
     * @throws ApiExceptions\BadStateException
     * @throws \InvalidArgumentException
     */
    public function createStructure(array $options): ItemInterface
    {
        $groupId = $options['group_id'];

        /** @var \Knp\Menu\ItemInterface|\Knp\Menu\ItemInterface[] $menu */
        $menu = $this->factory->createItem('root');

        $menu->setChildren([
            self::ITEM__CREATE => $this->createMenuItem(
                self::ITEM__CREATE,
                [
                    'attributes' => [
                        'class' => 'ibexa-btn--trigger',
                        'data-click' => '#object_state_create_create',
                    ],
                ]
            ),
            self::ITEM__CANCEL => $this->createMenuItem(
                self::ITEM__CANCEL,
                [
                    'route' => 'ezplatform.object_state.group.view',
                    'routeParameters' => [
                        'objectStateGroupId' => $groupId,
                    ],
                ]
            ),
        ]);

        return $menu;
    }

    /**
     * @return \JMS\TranslationBundle\Model\Message[]
     */
    public static function getTranslationMessages(): array
    {
        return [
            (new Message(self::ITEM__CREATE, 'menu'))->setDesc('Create'),
            (new Message(self::ITEM__CANCEL, 'menu'))->setDesc('Discard changes'),
        ];
    }
}
