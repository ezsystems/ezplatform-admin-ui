<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Menu;

use eZ\Publish\API\Repository\PermissionResolver;
use EzSystems\EzPlatformAdminUi\Menu\Event\ConfigureMenuEvent;
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
    const ITEM_USER_SETTINGS = 'user__settings';
    const ITEM_BOOKMARK = 'user__bookmark';
    const ITEM_DRAFTS = 'user__drafts';
    const ITEM_NOTIFICATION = 'menu.notification';

    /** @var \Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface */
    private $tokenStorage;

    /** @var \eZ\Publish\API\Repository\PermissionResolver */
    private $permissionResolver;

    /**
     * @param MenuItemFactory $factory
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher
     * @param \Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface $tokenStorage
     * @param \eZ\Publish\API\Repository\PermissionResolver $permissionResolver
     */
    public function __construct(
        MenuItemFactory $factory,
        EventDispatcherInterface $eventDispatcher,
        TokenStorageInterface $tokenStorage,
        PermissionResolver $permissionResolver
    ) {
        parent::__construct($factory, $eventDispatcher);

        $this->tokenStorage = $tokenStorage;
        $this->permissionResolver = $permissionResolver;
    }

    /**
     * @return string
     */
    protected function getConfigureEventName(): string
    {
        return ConfigureMenuEvent::USER_MENU;
    }

    /**
     * @param array $options
     *
     * @return \Knp\Menu\ItemInterface
     *
     * @throws \InvalidArgumentException
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
                $this->createMenuItem(self::ITEM_USER_SETTINGS, ['route' => 'ezplatform.user_settings.list'])
            );

            $menu->addChild(
                $this->createMenuItem(self::ITEM_BOOKMARK, ['route' => 'ezplatform.bookmark.list'])
            );

            if ($this->permissionResolver->hasAccess('content', 'versionread') !== false) {
                $menu->addChild(
                    $this->createMenuItem(self::ITEM_DRAFTS, [
                        'route' => 'ezplatform.content_draft.list',
                    ])
                );
            }

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

            $menu->addChild(
                $this->createMenuItem(self::ITEM_LOGOUT, ['route' => 'logout'])
            );
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
            (new Message(self::ITEM_USER_SETTINGS, 'menu'))->setDesc('User Settings'),
            (new Message(self::ITEM_BOOKMARK, 'menu'))->setDesc('Bookmarks'),
            (new Message(self::ITEM_DRAFTS, 'menu'))->setDesc('Drafts'),
            (new Message(self::ITEM_NOTIFICATION, 'notifications'))->setDesc('View Notifications'),
        ];
    }
}
