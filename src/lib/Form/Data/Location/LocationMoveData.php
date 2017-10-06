<?php

declare(strict_types=1);

namespace EzPlatformAdminUi\Form\Data\Location;

use eZ\Publish\API\Repository\Values\Content\Location;

/**
 * @todo add validation
 */
class LocationMoveData
{
    /** @var Location|null */
    protected $location;

    /** @var Location|null */
    protected $newParentLocation;

    /**
     * @param Location|null $location
     * @param Location|null $newParentLocation
     */
    public function __construct(?Location $location = null, Location $newParentLocation = null)
    {
        $this->location = $location;
        $this->newParentLocation = $newParentLocation;
    }

    /**
     * @return Location|null
     */
    public function getLocation(): ?Location
    {
        return $this->location;
    }

    /**
     * @param Location|null $location
     */
    public function setLocation(?Location $location)
    {
        $this->location = $location;
    }

    /**
     * @return Location|null
     */
    public function getNewParentLocation(): ?Location
    {
        return $this->newParentLocation;
    }

    /**
     * @param Location|null $newParentLocation
     */
    public function setNewParentLocation(?Location $newParentLocation)
    {
        $this->newParentLocation = $newParentLocation;
    }
}
