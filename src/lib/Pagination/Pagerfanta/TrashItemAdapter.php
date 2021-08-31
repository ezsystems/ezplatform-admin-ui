<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Pagination\Pagerfanta;

use eZ\Publish\API\Repository\TrashService;
use eZ\Publish\API\Repository\Values\Content\Query;
use Pagerfanta\Adapter\AdapterInterface;

/**
 * Pagerfanta adapter for eZ Publish content search.
 * Will return results as SearchHit objects.
 */
class TrashItemAdapter implements AdapterInterface
{
    /**
     * @var \eZ\Publish\API\Repository\Values\Content\Query
     */
    private $query;

    /**
     * @var \eZ\Publish\API\Repository\TrashService
     */
    private $trashService;

    /**
     * @var int
     */
    private $nbResults;

    public function __construct(Query $query, TrashService $trashService)
    {
        $this->query = $query;
        $this->trashService = $trashService;
    }

    /**
     * Returns the number of results.
     *
     * @return int the number of results
     */
    public function getNbResults()
    {
        if (isset($this->nbResults)) {
            return $this->nbResults;
        }

        $countQuery = clone $this->query;
        $countQuery->limit = 0;

        return $this->nbResults = $this->trashService->findTrashItems($countQuery)->count;
    }

    /**
     * Returns a slice of the results.
     *
     * @param int $offset the offset
     * @param int $length the length
     *
     * @return \eZ\Publish\API\Repository\Values\ValueObject[]
     */
    public function getSlice($offset, $length): array
    {
        $query = clone $this->query;
        $query->offset = $offset;
        $query->limit = $length;
        $query->performCount = false;

        $trashItems = $this->trashService->findTrashItems($query);

        if (null === $this->nbResults && null !== $trashItems->count) {
            $this->nbResults = $trashItems->count;
        }

        return $trashItems->items;
    }
}
