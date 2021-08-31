<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\EventListener;

use DateTime;
use DateTimeInterface;
use eZ\Publish\API\Repository\UserService;
use eZ\Publish\Core\MVC\Symfony\Security\UserInterface;
use EzSystems\EzPlatformAdminUi\Notification\NotificationHandlerInterface;
use EzSystems\EzPlatformAdminUi\Specification\SiteAccess\IsAdmin;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;
use Symfony\Contracts\Translation\TranslatorInterface;

final class CredentialsExpirationWarningListener implements EventSubscriberInterface
{
    /** @var \EzSystems\EzPlatformAdminUi\Notification\NotificationHandlerInterface */
    private $notificationHandler;

    /** @var \Symfony\Contracts\Translation\TranslatorInterface */
    private $translator;

    /** @var \Symfony\Component\Routing\Generator\UrlGeneratorInterface */
    private $urlGenerator;

    /** @var \eZ\Publish\API\Repository\UserService */
    private $userService;

    /** @var string[][] */
    private $siteAccessGroups;

    public function __construct(
        NotificationHandlerInterface $notificationHandler,
        TranslatorInterface $translator,
        UrlGeneratorInterface $urlGenerator,
        UserService $userService,
        array $siteAccessGroups
    ) {
        $this->notificationHandler = $notificationHandler;
        $this->translator = $translator;
        $this->urlGenerator = $urlGenerator;
        $this->siteAccessGroups = $siteAccessGroups;
        $this->userService = $userService;
    }

    public function onAuthenticationSuccess(InteractiveLoginEvent $event): void
    {
        if (!$this->isAdminSiteAccess($event->getRequest())) {
            return;
        }

        $user = $event->getAuthenticationToken()->getUser();
        if (!($user instanceof UserInterface)) {
            return;
        }

        $apiUser = $user->getAPIUser();

        $passwordInfo = $this->userService->getPasswordInfo($apiUser);
        if ($passwordInfo->hasExpirationWarningDate()) {
            $expirationWarningDate = $passwordInfo->getExpirationWarningDate();
            if ($expirationWarningDate <= new DateTime()) {
                $this->generateNotification($passwordInfo->getExpirationDate());
            }
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            SecurityEvents::INTERACTIVE_LOGIN => ['onAuthenticationSuccess', 12],
        ];
    }

    private function generateNotification(DateTimeInterface $passwordExpiresAt): void
    {
        $passwordExpiresIn = (new DateTime())->diff($passwordExpiresAt);

        if ($passwordExpiresIn->d > 0) {
            $warning = $this->translator->trans(
                /** @Desc("Your current password will expire in %days% day(s). You can change it in User Settings/My Account Settings.") */
                'authentication.credentials_expire_in.warning',
                [
                    '%days%' => $passwordExpiresIn->d + ($passwordExpiresIn->h >= 12 ? 1 : 0),
                    '%url%' => $this->urlGenerator->generate('ezplatform.user_profile.change_password'),
                ],
                'messages'
            );
        } else {
            $warning = $this->translator->trans(
                /** @Desc("Your current password will expire today. You can change it in User Settings/My Account Settings.") */
                'authentication.credentials_expire_today.warning',
                [
                    '%url%' => $this->urlGenerator->generate('ezplatform.user_profile.change_password'),
                ],
                'messages'
            );
        }

        $this->notificationHandler->warning($warning);
    }

    private function isAdminSiteAccess(Request $request): bool
    {
        return (new IsAdmin($this->siteAccessGroups))->isSatisfiedBy($request->attributes->get('siteaccess'));
    }
}
