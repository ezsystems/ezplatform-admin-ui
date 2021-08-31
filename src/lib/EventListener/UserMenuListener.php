<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\EventListener;

use eZ\Publish\API\Repository\PermissionResolver;
use eZ\Publish\API\Repository\UserService;
use EzSystems\EzPlatformAdminUi\Menu\Event\ConfigureMenuEvent;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @internal
 */
final class UserMenuListener implements EventSubscriberInterface, TranslationContainerInterface
{
    public const ITEM_CHANGE_PASSWORD = 'user__change_password';

    /** @var \eZ\Publish\API\Repository\PermissionResolver */
    private $permissionResolver;

    /** @var \eZ\Publish\API\Repository\UserService */
    private $userService;

    public function __construct(
        PermissionResolver $permissionResolver,
        UserService $userService
    ) {
        $this->permissionResolver = $permissionResolver;
        $this->userService = $userService;
    }

    public static function getSubscribedEvents(): array
    {
        return [ConfigureMenuEvent::USER_MENU => 'onUserMenuConfigure'];
    }

    public function onUserMenuConfigure(ConfigureMenuEvent $event): void
    {
        $menu = $event->getMenu();

        $currentUserId = $this->permissionResolver->getCurrentUserReference()->getUserId();
        $currentUser = $this->userService->loadUser($currentUserId);

        if ($this->permissionResolver->canUser('user', 'password', $currentUser, [$currentUser])) {
            $menu->addChild(
                self::ITEM_CHANGE_PASSWORD,
                [
                    'route' => 'ezplatform.user_profile.change_password',
                    'extras' => [
                        'translation_domain' => 'menu',
                        'orderNumber' => 40,
                    ],
                ]
            );
        }
    }

    /**
     * @return \JMS\TranslationBundle\Model\Message[]
     */
    public static function getTranslationMessages(): array
    {
        return [
            (new Message(self::ITEM_CHANGE_PASSWORD, 'menu'))->setDesc('Change password'),
        ];
    }
}
