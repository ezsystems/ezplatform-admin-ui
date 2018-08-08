<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Data\Location;

use eZ\Publish\API\Repository\Values\Content\Location;
use EzSystems\EzPlatformAdminUi\Form\Type\Location\LocationTrashWithAssetType;
use EzSystems\EzPlatformAdminUi\Validator\Constraints as AdminUiAssert;

class LocationTrashWithAssetData
{
    /**
     * @var Location|null
     * @AdminUiAssert\LocationHaveUniqueAssetRelation()
     */
    private $location;

    /** @var string */
    private $trashAssets;

    /**
     * @param Location|null $location
     */
    public function __construct(?Location $location = null)
    {
        $this->location = $location;
        $this->trashAssets = LocationTrashWithAssetType::RADIO_SELECT_DEFAULT_TRASH;
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
     * @return string
     */
    public function getTrashAssets(): string
    {
        return $this->trashAssets;
    }

    /**
     * @param string $trashAssets
     */
    public function setTrashAssets(string $trashAssets): void
    {
        $this->trashAssets = $trashAssets;
    }
}
