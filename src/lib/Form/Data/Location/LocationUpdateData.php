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
class LocationUpdateData
{
    /** @var Location|null */
    protected $location;

    /** @var string|null */
    protected $sortField;

    /** @var string|null */
    protected $sortOrder;

    /**
     * @param Location|null $location
     */
    public function __construct(?Location $location = null)
    {
        $this->location = $location;
        $this->sortField = $location->sortField ?? null;
        $this->sortOrder = $location->sortOrder ?? null;
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
     * @param null|int $sortField
     *
     * @return LocationUpdateData
     */
    public function setSortField(int $sortField): self
    {
        $this->sortField = $sortField;

        return $this;
    }

    /**
     * @return null|int
     */
    public function getSortField(): ?int
    {
        return $this->sortField;
    }

    /**
     * @param null|int $sortOrder
     *
     * @return LocationUpdateData
     */
    public function setSortOrder(int $sortOrder): self
    {
        $this->sortOrder = $sortOrder;

        return $this;
    }

    /**
     * @return null|int
     */
    public function getSortOrder(): ?int
    {
        return $this->sortOrder;
    }
}
