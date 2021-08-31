<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Data\Location;

use eZ\Publish\API\Repository\Values\Content\Location;

class LocationTrashData
{
    /** @var \eZ\Publish\API\Repository\Values\Content\Location|null */
    private $location;

    /** @var array|null */
    private $trashOptions;

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Location|null $location
     * @param array|null $trashOptions
     */
    public function __construct(
        ?Location $location = null,
        ?array $trashOptions = null
    ) {
        $this->location = $location;
        $this->trashOptions = $trashOptions;
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
    public function setLocation(?Location $location): void
    {
        $this->location = $location;
    }

    /**
     * @return array|null
     */
    public function getTrashOptions(): ?array
    {
        return $this->trashOptions;
    }

    /**
     * @param array|null $trashOptions
     */
    public function setTrashOptions(?array $trashOptions): void
    {
        $this->trashOptions = $trashOptions;
    }
}
