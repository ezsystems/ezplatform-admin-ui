<?php

namespace EzPlatformAdminUi\Service;

use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\TrashService as APITrashService;
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\TrashItem as APITrashItem;
use EzPlatformAdminUi\Form\Data\TrashItemData;

/**
 * @todo Loading all trash items should be handled by API service with our custom Query, this service will be removed.
 */
class TrashService
{
    /** @var APITrashService */
    protected $trashService;

    /** @var ContentTypeService */
    protected $contentTypeService;

    /** @var LocationService */
    protected $locationService;

    /** @var PathService */
    protected $pathService;

    /**
     * TrashService constructor.
     *
     * @param APITrashService $trashService
     * @param ContentTypeService $contentTypeService
     * @param LocationService $locationService
     * @param \EzPlatformAdminUi\Service\PathService $pathService
     */
    public function __construct(
        APITrashService $trashService,
        ContentTypeService $contentTypeService,
        LocationService $locationService,
        PathService $pathService
    ) {
        $this->trashService = $trashService;
        $this->contentTypeService = $contentTypeService;
        $this->locationService = $locationService;
        $this->pathService = $pathService;
    }

    /**
     * @return TrashItemData[]
     */
    public function loadTrashItems(): array
    {
        $query = new Query();
        $query->sortClauses = [new Query\SortClause\Location\Priority(Query::SORT_ASC)];
        $APITrashItemsList = $this->trashService->findTrashItems($query);
        $trashItemsList = [];
        /** @var APITrashItem $apiTrashItem */
        foreach ($APITrashItemsList->items as $apiTrashItem) {
            $contentType = $this->contentTypeService->loadContentType($apiTrashItem->contentInfo->contentTypeId);
            $ancestors = $this->pathService->loadPathLocations($apiTrashItem);

            $trashItemsList[] = new TrashItemData($apiTrashItem, $contentType, $ancestors);
        }
        return $trashItemsList;
    }
}
