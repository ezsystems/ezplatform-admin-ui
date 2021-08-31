<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Siteaccess;

use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\Core\MVC\Symfony\SiteAccess;
use EzSystems\EzPlatformAdminUi\Specification\SiteAccess\IsAdmin;

/**
 * Decorator for SiteaccessResolverInterface filtering out all non admin siteaccesses.
 */
class NonAdminSiteaccessResolver implements SiteaccessResolverInterface
{
    /** @var \EzSystems\EzPlatformAdminUi\Siteaccess\SiteaccessResolver */
    private $siteaccessResolver;

    /** @var string[] */
    private $siteAccessGroups;

    /**
     * @param \EzSystems\EzPlatformAdminUi\Siteaccess\SiteaccessResolver $siteaccessResolver
     * @param string[] $siteAccessGroups
     */
    public function __construct(SiteaccessResolver $siteaccessResolver, array $siteAccessGroups)
    {
        $this->siteaccessResolver = $siteaccessResolver;
        $this->siteAccessGroups = $siteAccessGroups;
    }

    public function getSiteaccessesForLocation(
        Location $location,
        int $versionNo = null,
        string $languageCode = null
    ): array {
        return $this->filter(
            $this->siteaccessResolver->getSiteaccessesForLocation($location, $versionNo, $languageCode)
        );
    }

    /**
     * @return \eZ\Publish\Core\MVC\Symfony\SiteAccess[]
     */
    public function getSiteAccessesListForLocation(
        Location $location,
        ?int $versionNo = null,
        ?string $languageCode = null
    ): array {
        return array_filter(
            $this->siteaccessResolver->getSiteAccessesListForLocation($location, $versionNo, $languageCode),
            function ($siteAccess) {
                return !$this->isAdminSiteAccess($siteAccess);
            }
        );
    }

    public function getSiteaccesses(): array
    {
        return $this->filter($this->siteaccessResolver->getSiteaccesses());
    }

    private function filter(array $siteaccesses): array
    {
        return array_diff($siteaccesses, $this->siteAccessGroups['admin_group']);
    }

    private function isAdminSiteAccess(SiteAccess $siteAccess): bool
    {
        return (new IsAdmin($this->siteAccessGroups))->isSatisfiedBy($siteAccess);
    }
}
