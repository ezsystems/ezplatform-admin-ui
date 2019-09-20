<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\EventListener;

use eZ\Publish\Core\MVC\Symfony\Event\PreContentViewEvent;
use eZ\Publish\Core\MVC\Symfony\MVCEvents;
use eZ\Publish\Core\MVC\Symfony\View\LoginFormView;
use EzSystems\EzPlatformAdminUi\Specification\SiteAccess\IsAdmin;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Exception\CredentialsExpiredException;

final class CredentialsExpiredListener implements EventSubscriberInterface
{
    /** @var \Symfony\Component\HttpFoundation\RequestStack */
    private $requestStack;

    /** @var string[][] */
    private $siteAccessGroups;

    /**
     * @param \Symfony\Component\HttpFoundation\RequestStack $requestStack
     * @param string[] $siteAccessGroups
     */
    public function __construct(RequestStack $requestStack, array $siteAccessGroups)
    {
        $this->requestStack = $requestStack;
        $this->siteAccessGroups = $siteAccessGroups;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            MVCEvents::PRE_CONTENT_VIEW => 'onPreContentView',
        ];
    }

    public function onPreContentView(PreContentViewEvent $event): void
    {
        if (!$this->isAdminSiteAccess($this->requestStack->getCurrentRequest())) {
            return;
        }

        $view = $event->getContentView();
        if (!($view instanceof LoginFormView)) {
            return;
        }

        if ($view->getLastAuthenticationException() instanceof CredentialsExpiredException) {
            $view->setTemplateIdentifier('@ezdesign/Security/error/credentials_expired.html.twig');
        }
    }

    private function isAdminSiteAccess(Request $request): bool
    {
        return (new IsAdmin($this->siteAccessGroups))->isSatisfiedBy($request->attributes->get('siteaccess'));
    }
}
