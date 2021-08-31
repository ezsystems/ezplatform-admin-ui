<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Permission;

use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\API\Repository\Values\User\LookupLimitationResult;

interface PermissionCheckerInterface
{
    public function getRestrictions(array $hasAccess, string $class): array;

    /**
     * @param array|bool $hasAccess
     */
    public function canCreateInLocation(Location $location, $hasAccess): bool;

    /**
     * @internal
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\BadStateException
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     */
    public function getContentCreateLimitations(Location $parentLocation): LookupLimitationResult;

    /**
     * @internal
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\BadStateException
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     */
    public function getContentUpdateLimitations(Location $parentLocation): LookupLimitationResult;
}
