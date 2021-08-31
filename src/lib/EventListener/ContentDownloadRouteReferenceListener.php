<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\EventListener;

use eZ\Publish\Core\MVC\Symfony\Event\RouteReferenceGenerationEvent;
use eZ\Publish\Core\MVC\Symfony\MVCEvents;
use eZ\Publish\Core\MVC\Symfony\SiteAccess;
use EzSystems\EzPlatformAdminUi\Specification\SiteAccess\IsAdmin;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Ensures that download urls generated in ezplatform-admin-ui are in the scope of admin siteaccess.
 *
 * @internal for internal use by AdminUI
 */
final class ContentDownloadRouteReferenceListener implements EventSubscriberInterface
{
    public const CONTENT_DOWNLOAD_ROUTE_NAME = 'ez_content_download';

    /** @var array */
    private $siteAccessGroups;

    public function __construct(array $siteAccessGroups)
    {
        $this->siteAccessGroups = $siteAccessGroups;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            MVCEvents::ROUTE_REFERENCE_GENERATION => 'onRouteReferenceGeneration',
        ];
    }

    public function onRouteReferenceGeneration(RouteReferenceGenerationEvent $event): void
    {
        $routeReference = $event->getRouteReference();

        if ($routeReference->getRoute() != self::CONTENT_DOWNLOAD_ROUTE_NAME) {
            return;
        }

        /** @var \eZ\Publish\Core\MVC\Symfony\SiteAccess $siteaccess */
        $siteaccess = $event->getRequest()->attributes->get('siteaccess');
        if ($this->isAdminSiteAccess($siteaccess)) {
            $routeReference->set('siteaccess', $siteaccess->name);
        }
    }

    private function isAdminSiteAccess(SiteAccess $siteAccess): bool
    {
        return (new IsAdmin($this->siteAccessGroups))->isSatisfiedBy($siteAccess);
    }
}
