<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Permission;

use eZ\Publish\API\Repository\Values\Content\Location;

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
}
