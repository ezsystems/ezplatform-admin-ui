<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Notification;

use Symfony\Component\Translation\TranslatorInterface;

final class TranslatableFlashBagNotificationHandler implements TranslatableNotificationHandlerInterface
{
    /**
     * @var \EzSystems\EzPlatformAdminUi\Notification\NotificationHandlerInterface
     */
    private $notificationHandler;
    /**
     *  @var \Symfony\Component\Translation\TranslatorInterface
     */
    private $translator;

    /**
     * @param \EzSystems\EzPlatformAdminUi\Notification\NotificationHandlerInterface $notificationHandler
     * @param \Symfony\Component\Translation\TranslatorInterface $translator
     */
    public function __construct(
        NotificationHandlerInterface $notificationHandler,
        TranslatorInterface $translator
    ) {
        $this->notificationHandler = $notificationHandler;
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function info(string $message, array $parameters, ?string $domain = null, ?string $locale = null): void
    {
        $translatedMessage = $this->translator->trans(
            /** @Ignore */
            $message,
            $parameters,
            $domain,
            $locale
        );
        $this->notificationHandler->info(/** @Ignore */ $translatedMessage);
    }

    /**
     * {@inheritdoc}
     */
    public function success(string $message, array $parameters, ?string $domain = null, ?string $locale = null): void
    {
        $translatedMessage = $this->translator->trans(
            /** @Ignore */
            $message,
            $parameters,
            $domain,
            $locale
        );
        $this->notificationHandler->success(/** @Ignore */ $translatedMessage);
    }

    /**
     * {@inheritdoc}
     */
    public function warning(string $message, array $parameters, ?string $domain = null, ?string $locale = null): void
    {
        $translatedMessage = $this->translator->trans(
            /** @Ignore */
            $message,
            $parameters,
            $domain,
            $locale
        );
        $this->notificationHandler->warning(/** @Ignore */ $translatedMessage);
    }

    /**
     * {@inheritdoc}
     */
    public function error(string $message, array $parameters, ?string $domain = null, ?string $locale = null): void
    {
        $translatedMessage = $this->translator->trans(
            /** @Ignore */
            $message,
            $parameters,
            $domain,
            $locale
        );
        $this->notificationHandler->error(/** @Ignore */ $translatedMessage);
    }
}
