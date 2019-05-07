<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Permission;

use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\API\Repository\Values\User\LookupLimitationResult;

interface PermissionCheckerInterface
{
    /**
     * @param $hasAccess
     * @param string $class
     *
     * @return array
     */
    public function getRestrictions($hasAccess, string $class): array;

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Location $location
     * @param array|bool $hasAccess
     *
     * @return bool
     */
    public function canCreateInLocation(Location $location, $hasAccess): bool;

    /**
     * @internal
     *
     * @param \eZ\Publish\API\Repository\Values\Content\Location $parentLocation
     *
     * @return \eZ\Publish\API\Repository\Values\User\LookupLimitationResult
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\BadStateException
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     */
    public function getContentCreateLimitations(Location $parentLocation): LookupLimitationResult;
}
