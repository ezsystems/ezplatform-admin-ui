<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Data\Trash;

use eZ\Publish\API\Repository\Values\Content\Location;
use EzSystems\EzPlatformAdminUi\Form\Data\TrashItemData;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @todo add validation
 */
class TrashItemRestoreData
{
    /**
     * @Assert\NotBlank()
     *
     * @var TrashItemData[]
     */
    public $trashItems;

    /** @var Location|null */
    public $location;

    /**
     * @param TrashItemData[] $trashItems
     * @param Location|null $location
     */
    public function __construct(array $trashItems = [], ?Location $location = null)
    {
        $this->trashItems = $trashItems;
        $this->location = $location;
    }

    /**
     * @return TrashItemData[]
     */
    public function getTrashItems(): array
    {
        return $this->trashItems;
    }

    /**
     * @param TrashItemData[] $trashItems
     */
    public function setTrashItems(array $trashItems)
    {
        $this->trashItems = $trashItems;
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
