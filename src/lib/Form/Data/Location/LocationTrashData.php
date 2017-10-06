<?php

declare(strict_types=1);

namespace EzPlatformAdminUi\Form\Data\Location;

use eZ\Publish\API\Repository\Values\Content\Location;

/**
 * @todo Add validation
 */
class LocationTrashData
{
    /** @var Location|null */
    private $location;

    /**
     * @param Location|null $location
     */
    public function __construct(?Location $location = null)
    {
        $this->location = $location;
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
}
