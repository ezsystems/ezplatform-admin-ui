<?php

namespace EzSystems\EzPlatformAdminUi\Form\Data;

use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\API\Repository\Values\Content\TrashItem as APITrashItem;
use eZ\Publish\API\Repository\Values\ContentType\ContentType;

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

    /**
     * TrashItemData constructor.
     *
     * @param APITrashItem $location
     * @param ContentType|null $contentType
     * @param Location[] $ancestors
     */
    public function __construct(
        APITrashItem $location,
        ContentType $contentType = null,
        array $ancestors = []
    ) {
        $this->location = $location;
        $this->contentType = $contentType;
        $this->ancestors = $ancestors;
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
        return !end($this->ancestors) instanceof Location;
    }
}
