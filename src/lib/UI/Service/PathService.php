<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\UI\Service;

use eZ\Publish\API\Repository\SearchService;
use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\Ancestor;
use eZ\Publish\API\Repository\Values\Content\Search\SearchHit;
use eZ\Publish\API\Repository\Values\Content\Location;

/**
 * Service for loading path information.
 *
 * @internal
 */
class PathService
{
    /** @var SearchService */
    private $searchService;

    public function __construct(SearchService $searchService)
    {
        $this->searchService = $searchService;
    }

    /**
     * Load path locations.
     *
     * @param Location $location
     *
     * @return Location[]
     */
    public function loadPathLocations(Location $location)
    {
        $locationQuery = new LocationQuery([
            'filter' => new Ancestor($location->pathString),
        ]);

        $searchResult = $this->searchService->findLocations($locationQuery);

        return array_map(function (SearchHit $searchHit) {
            return $searchHit->valueObject;
        }, $searchResult->searchHits);
    }
}
