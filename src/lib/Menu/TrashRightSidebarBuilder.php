<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Menu;

use eZ\Publish\API\Repository\Exceptions as ApiExceptions;
use eZ\Publish\API\Repository\PermissionResolver;
use eZ\Publish\API\Repository\TrashService;
use eZ\Publish\API\Repository\Values\Content\Query;
use EzSystems\EzPlatformAdminUi\Menu\Event\ConfigureMenuEvent;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * KnpMenuBundle Menu Builder service implementation for AdminUI Trash contextual sidebar menu.
 *
 * @see https://symfony.com/doc/current/bundles/KnpMenuBundle/menu_builder_service.html
 */
class TrashRightSidebarBuilder extends AbstractBuilder implements TranslationContainerInterface
{
    /* Menu items */
    const ITEM__EMPTY = 'trash__sidebar_right__empty_trash';

    /** @var \eZ\Publish\API\Repository\PermissionResolver */
    private $permissionResolver;

    /** @var \eZ\Publish\API\Repository\TrashService */
    private $trashService;

    /** @var \Symfony\Contracts\Translation\TranslatorInterface */
    private $translator;

    public function __construct(
        MenuItemFactory $factory,
        EventDispatcherInterface $eventDispatcher,
        PermissionResolver $permissionResolver,
        TrashService $trashService,
        TranslatorInterface $translator
    ) {
        parent::__construct($factory, $eventDispatcher);

        $this->permissionResolver = $permissionResolver;
        $this->trashService = $trashService;
        $this->translator = $translator;
    }

    /**
     * @return string
     */
    protected function getConfigureEventName(): string
    {
        return ConfigureMenuEvent::TRASH_SIDEBAR_RIGHT;
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
        /** @var bool $location */
        $canDelete = $this->permissionResolver->hasAccess('content', 'cleantrash');
        /** @var int $trashItemsCount */
        $trashItemsCount = $this->trashService->findTrashItems(new Query())->count;
        /** @var \Knp\Menu\ItemInterface|\Knp\Menu\ItemInterface[] $menu */
        $menu = $this->factory->createItem('root');

        $trashEmptyAttributes = [
            'data-bs-target' => '#confirmEmptyTrash',
            'data-bs-toggle' => 'modal',
        ];

        $menu->addChild(
            $this->createMenuItem(self::ITEM__EMPTY, [
                'attributes' => $canDelete > 0 && $trashItemsCount > 0
                    ? $trashEmptyAttributes
                    : ['class' => 'disabled'],
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
            (new Message(self::ITEM__EMPTY, 'menu'))->setDesc('Empty Trash'),
        ];
    }
}
