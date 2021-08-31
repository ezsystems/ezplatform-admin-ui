<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Data\Trash;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * @todo add validation
 */
class TrashItemDeleteData
{
    /**
     * @Assert\NotBlank()
     *
     * @var \eZ\Publish\API\Repository\Values\Content\TrashItem[]
     */
    public $trashItems;

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\TrashItem[] $trashItems
     */
    public function __construct(array $trashItems = [])
    {
        $this->trashItems = $trashItems;
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
    public function setTrashItems(array $trashItems): void
    {
        $this->trashItems = $trashItems;
    }
}
