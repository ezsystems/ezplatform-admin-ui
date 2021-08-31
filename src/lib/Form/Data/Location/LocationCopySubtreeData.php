<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Form\Data\Location;

use EzSystems\EzPlatformAdminUi\Validator\Constraints as AdminUiAssert;
use Symfony\Component\Validator\Constraints as Assert;

class LocationCopySubtreeData extends AbstractLocationCopyData
{
    /**
     * @var \eZ\Publish\API\Repository\Values\Content\Location|null
     *
     * @AdminUiAssert\LocationIsWithinCopySubtreeLimit()
     * @AdminUiAssert\LocationIsNotRoot()
     * @Assert\NotNull()
     */
    protected $location;

    /**
     * @var \eZ\Publish\API\Repository\Values\Content\Location|null
     *
     * @AdminUiAssert\LocationIsContainer()
     * @Assert\NotNull()
     * @AdminUiAssert\LocationIsNotSubLocation(
     *     propertyPath="location"
     * )
     */
    protected $newParentLocation;
}
