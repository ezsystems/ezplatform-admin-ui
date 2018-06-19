<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Menu;

use EzSystems\EzPlatformAdminUi\Menu\Event\ConfigureMenuEvent;
use InvalidArgumentException;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * KnpMenuBundle Menu Builder service implementation for AdminUI top menu.
 *
 * @see https://symfony.com/doc/current/bundles/KnpMenuBundle/menu_builder_service.html
 */
class UserMenuBuilder extends AbstractBuilder implements TranslationContainerInterface
{
    const ITEM_LOGOUT = 'user__content';
    const ITEM_CHANGE_PASSWORD = 'user__change_password';
    const ITEM_BOOKMARK = 'user__bookmark';
    const ITEM_NOTIFICATION = 'menu.notification';

    /** @var TokenStorageInterface */
    private $tokenStorage;

    /**
     * @param MenuItemFactory $factory
     * @param EventDispatcherInterface $eventDispatcher
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(
        MenuItemFactory $factory,
        EventDispatcherInterface $eventDispatcher,
        TokenStorageInterface $tokenStorage
    ) {
        parent::__construct($factory, $eventDispatcher);

        $this->tokenStorage = $tokenStorage;
    }

    protected function getConfigureEventName(): string
    {
        return ConfigureMenuEvent::USER_MENU;
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

        $token = $this->tokenStorage->getToken();
        if (null !== $token && is_object($token->getUser())) {
            $menu->addChild(
                $this->createMenuItem(self::ITEM_CHANGE_PASSWORD, ['route' => 'ezplatform.user_profile.change_password'])
            );
            $menu->addChild(
                $this->createMenuItem(self::ITEM_BOOKMARK, ['route' => 'ezplatform.bookmark.list'])
            );
            $menu->addChild(
                $this->createMenuItem(self::ITEM_LOGOUT, ['route' => 'logout'])
            );
            $menu->addChild(self::ITEM_NOTIFICATION, [
                'attributes' => [
                    'class' => 'ez-user-menu__item--notifications',
                    'data-toggle' => 'modal',
                    'data-target' => '#view-notifications',
                ],
                'extras' => [
                    'translation_domain' => 'notifications',
                    'template' => '@EzPlatformAdminUi/notifications/notifications_modal.html.twig',
                ],
            ]);
        }

        return $menu;
    }

    /**
     * @return array
     */
    public static function getTranslationMessages(): array
    {
        return [
            (new Message(self::ITEM_LOGOUT, 'menu'))->setDesc('Logout'),
            (new Message(self::ITEM_CHANGE_PASSWORD, 'menu'))->setDesc('Change password'),
            (new Message(self::ITEM_BOOKMARK, 'menu'))->setDesc('Bookmarks'),
            (new Message(self::ITEM_NOTIFICATION, 'notifications'))->setDesc('View Notifications'),
        ];
    }
}
