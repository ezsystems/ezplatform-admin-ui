<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Siteaccess;

use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\Core\Base\Exceptions\BadStateException;
use eZ\Publish\Core\MVC\ConfigResolverInterface;

/**
 * Decorator for SiteaccessResolverInterface filtering out all non admin siteaccesses.
 */
class NonAdminSiteaccessResolver implements SiteaccessResolverInterface
{
    /** @var SiteaccessResolver */
    private $siteaccessResolver;

    /** @var ConfigResolverInterface */
    private $configResolver;

    /**
     * @param SiteaccessResolver $siteaccessResolver
     * @param ConfigResolverInterface $configResolver
     */
    public function __construct(SiteaccessResolver $siteaccessResolver, ConfigResolverInterface $configResolver)
    {
        $this->siteaccessResolver = $siteaccessResolver;
        $this->configResolver = $configResolver;
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

    public function getSiteaccesses(): array
    {
        return $this->filter($this->siteaccessResolver->getSiteaccesses());
    }

    private function filter(array $siteaccesses)
    {
        $siteaccessGroups = $this->configResolver->getParameter(
            'groups',
            'ezpublish',
            'siteaccess'
        );

        if (!array_key_exists('admin_group', $siteaccessGroups)) {
            throw new BadStateException(
                'siteaccess',
                'Siteaccess group `admin_group` not found.'
            );
        }

        return array_diff($siteaccesses, $siteaccessGroups['admin_group']);
    }
}
