<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Siteaccess;

use eZ\Publish\API\Repository\Values\Content\Location;

interface SiteaccessResolverInterface
{
    /**
     * Accepts $location and returns all siteaccesses in which Content item can be previewed.
     *
     * @param \eZ\Publish\API\Repository\Values\Content\Location $location
     * @param int|null $versionNo
     * @param string|null $languageCode
     *
     * @return array
     */
    public function getSiteaccessesForLocation(
        Location $location,
        int $versionNo = null,
        string $languageCode = null
    ): array;

    /**
     * Returns complete list of siteaccesses.
     *
     * @return array
     */
    public function getSiteaccesses(): array;
}
