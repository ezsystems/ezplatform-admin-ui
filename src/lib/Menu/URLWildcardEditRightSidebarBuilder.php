<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Menu;

use EzSystems\EzPlatformAdminUi\Menu\Event\ConfigureMenuEvent;
use EzSystems\EzPlatformAdminUi\Tab\URLManagement\URLWildcardsTab;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class URLWildcardEditRightSidebarBuilder extends AbstractBuilder implements TranslationContainerInterface
{
    /* Menu items */
    const ITEM__SAVE = 'url_wildcard_edit__sidebar_right__save';
    const ITEM__CANCEL = 'url_wildcard_edit__sidebar_right__cancel';

    /** @var \Symfony\Contracts\Translation\TranslatorInterface */
    private $translator;

    /**
     * @param \EzSystems\EzPlatformAdminUi\Menu\MenuItemFactory $menuItemFactory
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher
     * @param \Symfony\Contracts\Translation\TranslatorInterface $translator
     */
    public function __construct(
        MenuItemFactory $menuItemFactory,
        EventDispatcherInterface $eventDispatcher,
        TranslatorInterface $translator
    ) {
        parent::__construct($menuItemFactory, $eventDispatcher);

        $this->translator = $translator;
    }

    public static function getTranslationMessages(): array
    {
        return [
            (new Message(self::ITEM__SAVE, 'menu'))->setDesc('Save'),
            (new Message(self::ITEM__CANCEL, 'menu'))->setDesc('Discard changes'),
        ];
    }

    protected function getConfigureEventName(): string
    {
        return ConfigureMenuEvent::URL_WILDCARD_EDIT_SIDEBAR_RIGHT;
    }

    protected function createStructure(array $options): ItemInterface
    {
        /** @var \Knp\Menu\ItemInterface $menu */
        $menu = $this->factory->createItem('root');

        $menu->setChildren([
            self::ITEM__SAVE => $this->createMenuItem(
                self::ITEM__SAVE,
                [
                    'attributes' => [
                        'class' => 'ibexa-btn--trigger',
                        'data-click' => $options['submit_selector'],
                    ],
                ]
            ),
            self::ITEM__CANCEL => $this->createMenuItem(
                self::ITEM__CANCEL,
                [
                    'route' => 'ezplatform.url_management',
                    'routeParameters' => [
                        '_fragment' => URLWildcardsTab::URI_FRAGMENT,
                    ],
                ]
            ),
        ]);

        return $menu;
    }
}
