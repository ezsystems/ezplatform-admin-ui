<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Data\Trash;

use eZ\Publish\API\Repository\Values\Content\Location;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @todo add validation
 */
class TrashItemRestoreData
{
    /**
     * @Assert\NotBlank()
     *
     * @var \eZ\Publish\API\Repository\Values\Content\TrashItem[]
     */
    public $trashItems;

    /** @var \eZ\Publish\API\Repository\Values\Content\Location|null */
    public $location;

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\TrashItem[] $trashItems
     * @param \eZ\Publish\API\Repository\Values\Content\Location|null $location
     */
    public function __construct(array $trashItems = [], ?Location $location = null)
    {
        $this->trashItems = $trashItems;
        $this->location = $location;
    }

    /**
     * @return \eZ\Publish\API\Repository\Values\Content\TrashItem[]
     */
    public function getTrashItems(): array
    {
        return $this->trashItems;
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\TrashItem[] $trashItems
     */
    public function setTrashItems(array $trashItems)
    {
        $this->trashItems = $trashItems;
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
    public function setLocation(?Location $location)
    {
        $this->location = $location;
    }
}
