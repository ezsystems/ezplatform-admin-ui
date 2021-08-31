<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\UI\Value\Location;

use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\Core\Repository\Values\Content\Location as CoreLocation;

class Bookmark extends CoreLocation
{
    /** @var \eZ\Publish\API\Repository\Values\ContentType\ContentType */
    protected $contentType;

    /** @var \eZ\Publish\API\Repository\Values\Content\Location[] */
    protected $pathLocations;

    /** @var bool */
    protected $userCanEdit;

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Location $location
     * @param array $properties
     */
    public function __construct(Location $location, array $properties = [])
    {
        parent::__construct(get_object_vars($location) + $properties);
    }
}
