<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Data\Location;

use eZ\Publish\API\Repository\Values\Content\Location;
use EzSystems\EzPlatformAdminUi\Validator\Constraints as AdminUiAssert;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @deprecated since 2.5, to be removed in 3.0.
 */
class LocationTrashWithAssetData
{
    /**
     * @var Location|null
     *
     * @AdminUiAssert\LocationHaveUniqueAssetRelation()
     */
    private $location;

    /**
     * @var string|null
     *
     * @Assert\NotBlank()
     */
    private $trashAssets;

    /**
     * @param Location|null $location
     * @param string|null $trashAssets
     */
    public function __construct(?Location $location = null, ?string $trashAssets = null)
    {
        $this->location = $location;
        $this->trashAssets = $trashAssets;
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
    public function setLocation(?Location $location): void
    {
        $this->location = $location;
    }

    /**
     * @return string|null
     */
    public function getTrashAssets(): ?string
    {
        return $this->trashAssets;
    }

    /**
     * @param string|null $trashAssets
     */
    public function setTrashAssets(?string $trashAssets): void
    {
        $this->trashAssets = $trashAssets;
    }
}
