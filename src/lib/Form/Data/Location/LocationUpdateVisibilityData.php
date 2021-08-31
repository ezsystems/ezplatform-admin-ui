<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Data\Location;

use eZ\Publish\API\Repository\Values\Content\Location;

/**
 * @todo Add validation
 */
class LocationUpdateVisibilityData
{
    /** @var \eZ\Publish\API\Repository\Values\Content\Location|null */
    private $location;

    /** @var bool|null */
    private $hidden;

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Location|null $location
     */
    public function __construct(?Location $location = null)
    {
        if (null === $location) {
            return;
        }

        $this->location = $location;
        $this->hidden = $location->hidden;
    }

    /**
     * @return \eZ\Publish\API\Repository\Values\Content\Location|null
     */
    public function getLocation(): ?Location
    {
        return $this->location;
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Location|null $location
     */
    public function setLocation(?Location $location)
    {
        $this->location = $location;
    }

    /**
     * @return bool|null
     */
    public function getHidden(): ?bool
    {
        return $this->hidden;
    }

    /**
     * @param bool|null $hidden
     */
    public function setHidden($hidden)
    {
        $this->hidden = $hidden;
    }
}
