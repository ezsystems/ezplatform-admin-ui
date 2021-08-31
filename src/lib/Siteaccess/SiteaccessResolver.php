<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Siteaccess;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\Core\MVC\Symfony\SiteAccess\SiteAccessService;

class SiteaccessResolver implements SiteaccessResolverInterface
{
    /** @var \eZ\Publish\API\Repository\ContentService */
    private $contentService;

    /** @var \EzSystems\EzPlatformAdminUi\Siteaccess\SiteaccessPreviewVoterInterface[] */
    private $siteAccessPreviewVoters;

    /** @var \eZ\Publish\Core\MVC\Symfony\SiteAccess\SiteAccessService */
    private $siteAccessService;

    /**
     * @param \eZ\Publish\API\Repository\ContentService $contentService
     * @param iterable $siteaccessPreviewVoters
     * @param array $siteAccesses
     */
    public function __construct(
        ContentService $contentService,
        iterable $siteaccessPreviewVoters,
        SiteAccessService $siteAccessService
    ) {
        $this->contentService = $contentService;
        $this->siteAccessPreviewVoters = $siteaccessPreviewVoters;
        $this->siteAccessService = $siteAccessService;
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Location $location
     * @param int|null $versionNo
     * @param string|null $languageCode
     *
     * @return array
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
    public function getSiteaccessesForLocation(
        Location $location,
        int $versionNo = null,
        string $languageCode = null
    ): array {
        return $this->getSiteAccessList(
            $this->getSiteAccessesListForLocation($location, $versionNo, $languageCode)
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
        $contentInfo = $location->getContentInfo();
        $versionInfo = $this->contentService->loadVersionInfo($contentInfo, $versionNo);
        $languageCode = $languageCode ?? $contentInfo->mainLanguageCode;

        $eligibleSiteAccesses = [];
        /** @var \eZ\Publish\Core\MVC\Symfony\SiteAccess $siteAccess */
        foreach ($this->siteAccessService->getAll() as $siteAccess) {
            $context = new SiteaccessPreviewVoterContext($location, $versionInfo, $siteAccess->name, $languageCode);
            foreach ($this->siteAccessPreviewVoters as $siteAccessPreviewVoter) {
                if ($siteAccessPreviewVoter->vote($context)) {
                    $eligibleSiteAccesses[] = $siteAccess;
                    break;
                }
            }
        }

        return $eligibleSiteAccesses;
    }

    public function getSiteaccesses(): array
    {
        $siteAccessList = iterator_to_array($this->siteAccessService->getAll());

        return $this->getSiteAccessList($siteAccessList);
    }

    /**
     * @return string[]
     */
    private function getSiteAccessList(array $siteAccessList): array
    {
        return array_column(
            $siteAccessList,
            'name'
        );
    }
}
