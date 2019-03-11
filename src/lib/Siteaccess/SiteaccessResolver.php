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
    /** @var \eZ\Publish\Core\MVC\ConfigResolverInterface */
    private $configResolver;

    /** @var \eZ\Publish\API\Repository\ContentService */
    private $contentService;

    /** @var \EzSystems\EzPlatformAdminUi\Siteaccess\SiteaccessPreviewVoterInterface[] */
    private $siteaccessPreviewVoters;

    /**
     * @param \eZ\Publish\Core\MVC\ConfigResolverInterface $configResolver
     * @param \eZ\Publish\API\Repository\ContentService $contentService
     * @param iterable $siteaccessPreviewVoters
     */
    public function __construct(
        ConfigResolverInterface $configResolver,
        ContentService $contentService,
        iterable $siteaccessPreviewVoters
    ) {
        $this->configResolver = $configResolver;
        $this->contentService = $contentService;
        $this->siteaccessPreviewVoters = $siteaccessPreviewVoters;
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
        $contentInfo = $location->getContentInfo();
        $versionInfo = $this->contentService->loadVersionInfo($contentInfo, $versionNo);
        $languageCode = $languageCode ?? $contentInfo->mainLanguageCode;

        $eligibleSiteaccesses = [];
        foreach ($this->getSiteaccesses() as $siteaccess) {
            $context = new SiteaccessPreviewVoterContext($location, $versionInfo, $siteaccess, $languageCode);
            foreach ($this->siteaccessPreviewVoters as $siteaccessPreviewVoter) {
                if ($siteaccessPreviewVoter->vote($context)) {
                    $eligibleSiteaccesses[] = $siteaccess;
                    break;
                }
            }
        }

        return $eligibleSiteaccesses;
    }

    public function getSiteaccesses(): array
    {
        return $this->configResolver->getParameter('list', 'ezpublish', 'siteaccess');
    }
}
