<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\EventListener;

use EzSystems\EzPlatformAdminUi\Menu\Event\ConfigureMenuEvent;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserPasswordChangeRightSidebarListener implements EventSubscriberInterface, TranslationContainerInterface
{
    /* Menu items */
    public const ITEM__UPDATE = 'user_password_change__sidebar_right__update';
    public const ITEM__CANCEL = 'user_password_change__sidebar_right__cancel';

    /** @var \Symfony\Contracts\Translation\TranslatorInterface */
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [ConfigureMenuEvent::USER_PASSWORD_CHANGE_SIDEBAR_RIGHT => 'onUserPasswordChangeRightSidebarConfigure'];
    }

    /**
     * @param \EzSystems\EzPlatformAdminUi\Menu\Event\ConfigureMenuEvent $event
     */
    public function onUserPasswordChangeRightSidebarConfigure(ConfigureMenuEvent $event): void
    {
        $menu = $event->getMenu();

        $menu->addChild(
            self::ITEM__UPDATE,
            [
                'attributes' => [
                    'class' => 'ibexa-btn--trigger',
                    'data-click' => '#user_password_change_change',
                ],
                'extras' => ['icon' => 'publish', 'translation_domain' => 'menu'],
            ]
        );
        $menu->addChild(
            self::ITEM__CANCEL,
            [
                'extras' => ['icon' => 'circle-close', 'translation_domain' => 'menu'],
                'route' => 'ezplatform.dashboard',
            ]
        );
    }

    /**
     * @return \JMS\TranslationBundle\Model\Message[]
     */
    public static function getTranslationMessages(): array
    {
        return [
            (new Message(self::ITEM__UPDATE, 'menu'))->setDesc('Update'),
            (new Message(self::ITEM__CANCEL, 'menu'))->setDesc('Discard changes'),
        ];
    }
}
