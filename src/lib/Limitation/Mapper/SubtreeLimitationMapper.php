<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Limitation\Mapper;

use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\Ancestor;
use eZ\Publish\API\Repository\Values\Content\Query\SortClause\Location\Path;
use eZ\Publish\API\Repository\Values\User\Limitation;

class SubtreeLimitationMapper extends UDWBasedMapper
{
    public function filterLimitationValues(Limitation $limitation)
    {
        if (!is_array($limitation->limitationValues)) {
            return;
        }

        // UDW returns an array of location IDs. If we haven't used UDW, the value is as stored: an array of path strings.
        foreach ($limitation->limitationValues as $key => $limitationValue) {
            if (preg_match('/\A\d+\z/', $limitationValue) === 1) {
                $limitation->limitationValues[$key] = $this->locationService->loadLocation($limitationValue)->pathString;
            }
        }
    }

    public function mapLimitationValue(Limitation $limitation)
    {
        $values = [];

        foreach ($limitation->limitationValues as $pathString) {
            $query = new LocationQuery([
                'filter' => new Ancestor($pathString),
                'sortClauses' => [new Path()],
            ]);

            $path = [];
            foreach ($this->searchService->findLocations($query)->searchHits as $hit) {
                $path[] = $hit->valueObject->getContentInfo();
            }

            $values[] = $path;
        }

        return $values;
    }
}
