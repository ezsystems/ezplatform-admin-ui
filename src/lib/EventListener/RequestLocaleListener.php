<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\EventListener;

use EzSystems\EzPlatformAdminUi\Specification\SiteAccess\IsAdmin;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Translation\TranslatorInterface;

class RequestLocaleListener implements EventSubscriberInterface
{
    /** @var array */
    private $siteAccessGroups;

    /** @var array */
    private $availableTranslations;

    /** @var \Symfony\Component\Translation\TranslatorInterface */
    private $translator;

    /**
     * @param array $siteAccessGroups
     * @param array $availableTranslations
     * @param \Symfony\Component\Translation\TranslatorInterface $translator
     */
    public function __construct(
        array $siteAccessGroups,
        array $availableTranslations,
        TranslatorInterface $translator
    ) {
        $this->siteAccessGroups = $siteAccessGroups;
        $this->availableTranslations = $availableTranslations;
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 6],
        ];
    }

    /**
     * @param \Symfony\Component\HttpKernel\Event\GetResponseEvent $event
     *
     * @throws \EzSystems\EzPlatformAdminUi\Exception\InvalidArgumentException
     */
    public function onKernelRequest(GetResponseEvent $event): void
    {
        $request = $event->getRequest();

        if (HttpKernelInterface::MASTER_REQUEST !== $event->getRequestType() || !$this->isAdminSiteAccess($request)) {
            return;
        }

        $preferableLocales = $this->getPreferredLocales($request);
        $locale = null;

        foreach ($preferableLocales as $preferableLocale) {
            if (\in_array($preferableLocale, $this->availableTranslations)) {
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

    /**
     * Return array of preferred user locales.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return array
     */
    protected function getPreferredLocales(Request $request): array
    {
        $preferredLocales = $request->headers->get('Accept-Language') ?? '';
        $preferredLocales = array_reduce(
            explode(',', $preferredLocales),
            function ($res, $el) {
                [$l, $q] = array_merge(explode(';q=', $el), [1]);
                $res[$l] = (float) $q;

                return $res;
            }, []);
        arsort($preferredLocales);

        return array_keys($preferredLocales);
    }
}
