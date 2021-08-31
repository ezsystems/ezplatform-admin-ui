<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Specification\Location;

use eZ\Publish\API\Repository\SearchService;
use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use EzSystems\EzPlatformAdminUi\Specification\AbstractSpecification;

class IsWithinCopySubtreeLimit extends AbstractSpecification
{
    /** @var int */
    private $copyLimit;

    /** @var \eZ\Publish\API\Repository\LocationService */
    private $searchService;

    /**
     * @param int $copyLimit
     * @param \eZ\Publish\API\Repository\SearchService $searchService
     */
    public function __construct(
        int $copyLimit,
        SearchService $searchService
    ) {
        $this->copyLimit = $copyLimit;
        $this->searchService = $searchService;
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Location $item
     *
     * @return bool
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     */
    public function isSatisfiedBy($item): bool
    {
        if ($this->copyLimit === -1) {
            return true;
        }

        if ($this->copyLimit === 0) {
            return false;
        }

        $query = new LocationQuery([
            'filter' => new Criterion\Subtree($item->pathString),
            'limit' => 0,
        ]);

        $searchResults = $this->searchService->findLocations($query);

        if ($this->copyLimit >= $searchResults->totalCount) {
            return true;
        }

        return false;
    }
}
