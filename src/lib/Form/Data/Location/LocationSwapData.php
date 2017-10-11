<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Data\Location;

use eZ\Publish\API\Repository\Values\Content\Location;

/**
 * @todo add validation
 */
class LocationSwapData
{
    /** @var Location|null */
    protected $currentLocation;

    /** @var Location|null */
    protected $newLocation;

    /**
     * @param Location|null $currentLocation
     * @param Location|null $newLocation
     */
    public function __construct(?Location $currentLocation = null, Location $newLocation = null)
    {
        $this->currentLocation = $currentLocation;
        $this->newLocation = $newLocation;
    }

    /**
     * @return Location|null
     */
    public function getCurrentLocation(): ?Location
    {
        return $this->currentLocation;
    }

    /**
     * @param Location|null $currentLocation
     */
    public function setCurrentLocation(?Location $currentLocation)
    {
        $this->currentLocation = $currentLocation;
    }

    /**
     * @return Location|null
     */
    public function getNewLocation(): ?Location
    {
        return $this->newLocation;
    }

    /**
     * @param Location|null $newLocation
     */
    public function setNewLocation(?Location $newLocation)
    {
        $this->newLocation = $newLocation;
    }
}
