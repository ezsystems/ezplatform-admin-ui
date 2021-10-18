<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Menu\Admin\Role;

use EzSystems\EzPlatformAdminUi\Menu\AbstractBuilder;
use EzSystems\EzPlatformAdminUi\Menu\Event\ConfigureMenuEvent;
use EzSystems\EzPlatformAdminUi\Menu\MenuItemFactory;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * KnpMenuBundle Menu Builder service implementation for AdminUI Section Copy contextual sidebar menu.
 *
 * @see https://symfony.com/doc/current/bundles/KnpMenuBundle/menu_builder_service.html
 */
class RoleCopyRightSidebarBuilder extends AbstractBuilder implements TranslationContainerInterface
{
    /* Menu items */
    const ITEM__SAVE = 'role_copy__sidebar_right__save';
    const ITEM__CANCEL = 'role_copy__sidebar_right__cancel';

    /** @var \Symfony\Contracts\Translation\TranslatorInterface */
    private $translator;

    public function __construct(
        MenuItemFactory $factory,
        EventDispatcherInterface $eventDispatcher,
        TranslatorInterface $translator
    ) {
        parent::__construct($factory, $eventDispatcher);

        $this->translator = $translator;
    }

    protected function getConfigureEventName(): string
    {
        return ConfigureMenuEvent::ROLE_COPY_SIDEBAR_RIGHT;
    }

    /**
     * @throws \InvalidArgumentException
     */
    public function createStructure(array $options): ItemInterface
    {
        /** @var \Knp\Menu\ItemInterface|\Knp\Menu\ItemInterface[] $menu */
        $menu = $this->factory->createItem('root');

        $menu->setChildren([
            self::ITEM__SAVE => $this->createMenuItem(
                self::ITEM__SAVE,
                [
                    'attributes' => [
                        'class' => 'ibexa-btn--trigger',
                        'data-click' => '#role_copy_copy',
                        'data-extra-classes' => 'ez-tooltip--medium',
                        'data-placement' => 'left',
                        'title' => $this->translator->trans(
/** @Ignore */ self::ITEM__SAVE,
                            [],
                            'menu'
                        ),
                    ],
                ]
            ),
            self::ITEM__CANCEL => $this->createMenuItem(
                self::ITEM__CANCEL,
                [
                    'attributes' => [
                        'data-extra-classes' => 'ez-tooltip--medium',
                        'data-placement' => 'left',
                        'title' => $this->translator->trans(
/** @Ignore */ self::ITEM__CANCEL,
                            [],
                            'menu'
                        ),
                    ],
                    'route' => 'ezplatform.role.list',
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
            (new Message(self::ITEM__SAVE, 'menu'))->setDesc('Copy'),
            (new Message(self::ITEM__CANCEL, 'menu'))->setDesc('Discard changes'),
        ];
    }
}
