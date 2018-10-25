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

class LocationTrashContainerData
{
    /**
     * @var Location|null
     * @AdminUiAssert\LocationIsContainer()
     * @AdminUiAssert\LocationHasChildren()
     */
    private $location;

    /**
     * @var array|null
     *
     * @Assert\NotBlank()
     */
    private $trashContainer;

    /**
     * @param Location|null $location
     * @param array|null $trashContainer
     */
    public function __construct(?Location $location = null, ?array $trashContainer = null)
    {
        $this->location = $location;
        $this->trashContainer = $trashContainer;
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
     * @return array|null
     */
    public function getTrashContainer(): ?array
    {
        return $this->trashContainer;
    }

    /**
     * @param array|null $trashContainer
     */
    public function setTrashContainer(?array $trashContainer): void
    {
        $this->trashContainer = $trashContainer;
    }
}
