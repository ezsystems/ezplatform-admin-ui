<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Validator\Constraints;

use Symfony\Component\Validator\Constraints\AbstractComparisonValidator;

class LocationIsNotSubLocationValidator extends AbstractComparisonValidator
{
    /**
     * Compares the two given values to find if their relationship is valid.
     *
     * @param \eZ\Publish\API\Repository\Values\Content\Location $targetLocation
     * @param \eZ\Publish\API\Repository\Values\Content\Location $sourceLocation
     *
     * @return bool true if the relationship is valid, false otherwise
     */
    protected function compareValues($targetLocation, $sourceLocation)
    {
        return stripos($targetLocation->pathString, $sourceLocation->pathString) === false;
    }
}
