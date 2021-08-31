<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\EventListener;

use eZ\Publish\Core\MVC\ConfigResolverInterface;
use eZ\Publish\Core\MVC\Symfony\Locale\UserLanguagePreferenceProviderInterface;
use EzSystems\EzPlatformAdminUi\Specification\SiteAccess\IsAdmin;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Contracts\Translation\TranslatorInterface;

class RequestLocaleListener implements EventSubscriberInterface
{
    /** @var array */
    private $siteAccessGroups;

    /** @var array */
    private $availableTranslations;

    /** @var \Symfony\Contracts\Translation\TranslatorInterface */
    private $translator;

    /** @var \eZ\Publish\Core\MVC\Symfony\Locale\UserLanguagePreferenceProviderInterface */
    private $userLanguagePreferenceProvider;

    /** @var \eZ\Publish\Core\MVC\ConfigResolverInterface */
    private $configResolver;

    /**
     * @param array $siteAccessGroups
     * @param array $availableTranslations
     * @param \Symfony\Contracts\Translation\TranslatorInterface $translator
     * @param \eZ\Publish\Core\MVC\Symfony\Locale\UserLanguagePreferenceProviderInterface $userLanguagePreferenceProvider
     * @param \eZ\Publish\Core\MVC\ConfigResolverInterface $configResolver
     */
    public function __construct(
        array $siteAccessGroups,
        array $availableTranslations,
        TranslatorInterface $translator,
        UserLanguagePreferenceProviderInterface $userLanguagePreferenceProvider,
        ConfigResolverInterface $configResolver
    ) {
        $this->siteAccessGroups = $siteAccessGroups;
        $this->availableTranslations = $availableTranslations;
        $this->translator = $translator;
        $this->userLanguagePreferenceProvider = $userLanguagePreferenceProvider;
        $this->configResolver = $configResolver;
    }

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 6],
        ];
    }

    /**
     * @param \Symfony\Component\HttpKernel\Event\RequestEvent $event
     *
     * @throws \EzSystems\EzPlatformAdminUi\Exception\InvalidArgumentException
     */
    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();

        if (HttpKernelInterface::MASTER_REQUEST !== $event->getRequestType() || !$this->isAdminSiteAccess($request)) {
            return;
        }

        $additionalTranslations = $this->configResolver->getParameter('user_preferences.additional_translations');
        $preferableLocales = $this->userLanguagePreferenceProvider->getPreferredLocales($request);
        $locale = null;

        foreach ($preferableLocales as $preferableLocale) {
            if (\in_array($preferableLocale, $this->availableTranslations, true)
                || \in_array($preferableLocale, $additionalTranslations, true)
            ) {
                $locale = $preferableLocale;
                break;
            }
        }
        $locale = $locale ?? reset($preferableLocales);
        $request->setLocale($locale);
        $request->attributes->set('_locale', $locale);
        // Set of the current locale on the translator service is needed because RequestLocaleListener has lower
        // priority than LocaleListener and messages are not translated on login, forgot and reset password pages.
        $this->translator->setLocale($locale);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return bool
     *
     * @throws \EzSystems\EzPlatformAdminUi\Exception\InvalidArgumentException
     */
    protected function isAdminSiteAccess(Request $request): bool
    {
        return (new IsAdmin($this->siteAccessGroups))->isSatisfiedBy($request->attributes->get('siteaccess'));
    }
}
