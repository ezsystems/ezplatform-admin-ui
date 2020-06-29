<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Form\Data;

use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\API\Repository\Values\Content\TrashItem as APITrashItem;
use eZ\Publish\API\Repository\Values\ContentType\ContentType;
use eZ\Publish\API\Repository\Values\User\User;

/**
 * @todo This class cannot be a part of Form/ namespace, it should be moved to UI/Value.
 */
class TrashItemData
{
    /** @var APITrashItem */
    protected $location;

    /** @var ContentType */
    protected $contentType;

    /** @var Location[] */
    protected $ancestors;

    /** @var \eZ\Publish\API\Repository\Values\User\User */
    private $creator;

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Location[] $ancestors
     */
    public function __construct(
        APITrashItem $location,
        ContentType $contentType = null,
        array $ancestors = [],
        ?User $creator = null
    ) {
        $this->location = $location;
        $this->contentType = $contentType;
        $this->ancestors = $ancestors;
        $this->creator = $creator;
    }

    /**
     * @return APITrashItem
     */
    public function getLocation(): APITrashItem
    {
        return $this->location;
    }

    /**
     * @param APITrashItem $location
     */
    public function setLocation(APITrashItem $location)
    {
        $this->location = $location;
    }

    /**
     * @return ContentType
     */
    public function getContentType(): ContentType
    {
        return $this->contentType;
    }

    /**
     * @param ContentType $contentType
     */
    public function setContentType(ContentType $contentType)
    {
        $this->contentType = $contentType;
    }

    /**
     * @return Location[]
     */
    public function getAncestors(): array
    {
        return $this->ancestors;
    }

    /**
     * @param Location[] $ancestors
     */
    public function setAncestors(array $ancestors)
    {
        $this->ancestors = $ancestors;
    }

    public function isParentInTrash(): bool
    {
        $lastAncestor = end($this->ancestors);

        return $this->location->path !== array_merge($lastAncestor->path, [(string)$this->location->id]);
    }

    public function getCreator(): ?User
    {
        return $this->creator;
    }
}
