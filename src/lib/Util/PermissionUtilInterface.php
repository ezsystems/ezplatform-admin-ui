<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Util;

use eZ\Publish\API\Repository\Values\Content\Location;

interface PermissionUtilInterface
{
    /**
     * This method should only be used for very specific use cases. It should be used in a content cases
     * where assignment limitations are not relevant.
     *
     * @param array $hasAccess
     *
     * @return array
     */
    public function flattenArrayOfLimitations(array $hasAccess): array;

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
