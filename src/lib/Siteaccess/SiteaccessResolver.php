<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Siteaccess;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\Core\MVC\ConfigResolverInterface;

class SiteaccessResolver implements SiteaccessResolverInterface
{
    /** @var ConfigResolverInterface */
    private $configResolver;

    /** @var ContentService */
    private $contentService;

    /**
     * @param ConfigResolverInterface $configResolver
     * @param ContentService $contentService
     */
    public function __construct(ConfigResolverInterface $configResolver, ContentService $contentService)
    {
        $this->configResolver = $configResolver;
        $this->contentService = $contentService;
    }

    public function getSiteaccessesForLocation(
        Location $location,
        int $versionNo = null,
        string $languageCode = null
    ): array {
        $languageCode = $languageCode ?? $location->getContentInfo()->mainLanguageCode;
        $versionInfo = $this->contentService->loadVersionInfo($location->getContentInfo(), $versionNo);
        $contentLanguages = $versionInfo->languageCodes;

        $eligibleSiteaccesses = [];
        foreach ($this->getSiteaccesses() as $siteaccess) {
            $rootLocationId = $this->configResolver->getParameter(
                'content.tree_root.location_id',
                null,
                $siteaccess
            );
            if (!in_array($rootLocationId, $location->path)) {
                continue;
            }

            $siteaccessLanguages = $this->configResolver->getParameter(
                'languages',
                null,
                $siteaccess
            );
            if (!in_array($languageCode, $siteaccessLanguages)) {
                continue;
            }

            $primarySiteaccessLanguage = reset($siteaccessLanguages);
            if (
                $languageCode !== $primarySiteaccessLanguage
                && in_array($primarySiteaccessLanguage, $contentLanguages)
            ) {
                continue;
            }

            $eligibleSiteaccesses[] = $siteaccess;
        }

        return $eligibleSiteaccesses;
    }

    public function getSiteaccesses(): array
    {
        return $this->configResolver->getParameter('list', 'ezpublish', 'siteaccess');
    }
}
