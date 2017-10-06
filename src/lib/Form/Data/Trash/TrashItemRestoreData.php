<?php

declare(strict_types=1);

namespace EzPlatformAdminUi\Form\Data\Trash;

use eZ\Publish\API\Repository\Values\Content\Location;
use EzPlatformAdminUi\Form\Data\TrashItemData;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @todo add validation
 */
class TrashItemRestoreData
{
    /**
     * @Assert\NotBlank()
     * @var TrashItemData[]
     */
    public $trashItems;

    /**
     * @var Location|null
     */
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
