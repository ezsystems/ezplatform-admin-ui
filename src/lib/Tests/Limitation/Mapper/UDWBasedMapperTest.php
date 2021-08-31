<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Tests\Limitation\Mapper;

use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\SearchService;
use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\Ancestor;
use eZ\Publish\API\Repository\Values\Content\Query\SortClause\Location\Path;
use eZ\Publish\API\Repository\Values\Content\Search\SearchHit;
use eZ\Publish\API\Repository\Values\Content\Search\SearchResult;
use eZ\Publish\API\Repository\Values\User\Limitation\SubtreeLimitation;
use eZ\Publish\Core\Repository\Values\Content\Location;
use EzSystems\EzPlatformAdminUi\Limitation\Mapper\UDWBasedMapper;
use PHPUnit\Framework\TestCase;

class UDWBasedMapperTest extends TestCase
{
    public function testMapLimitationValue()
    {
        $values = [5, 7, 11];
        $expected = [
            [
                new ContentInfo(['id' => 1]),
                new ContentInfo(['id' => 2]),
                new ContentInfo(['id' => 5]),
            ],
            [
                new ContentInfo(['id' => 1]),
                new ContentInfo(['id' => 2]),
                new ContentInfo(['id' => 7]),
            ],
            [
                new ContentInfo(['id' => 1]),
                new ContentInfo(['id' => 2]),
                new ContentInfo(['id' => 11]),
            ],
        ];

        $locationServiceMock = $this->createMock(LocationService::class);
        $searchServiceMock = $this->createMock(SearchService::class);

        foreach ($values as $i => $id) {
            $location = new Location([
                'pathString' => '/1/2/' . $id . '/',
            ]);

            $locationServiceMock
                ->expects($this->at($i))
                ->method('loadLocation')
                ->with($id)
                ->willReturn($location);

            $query = new LocationQuery([
                'filter' => new Ancestor($location->pathString),
                'sortClauses' => [new Path()],
            ]);

            $searchServiceMock
                ->expects($this->at($i))
                ->method('findLocations')
                ->with($query)
                ->willReturn($this->createSearchResultsMock($expected[$i]));
        }

        $mapper = new UDWBasedMapper($locationServiceMock, $searchServiceMock);
        $result = $mapper->mapLimitationValue(new SubtreeLimitation([
            'limitationValues' => $values,
        ]));

        $this->assertEquals($expected, $result);
    }

    private function createSearchResultsMock($expected)
    {
        $hits = [];
        foreach ($expected as $contentInfo) {
            $locationMock = $this->createMock(Location::class);
            $locationMock
                ->expects($this->atLeastOnce())
                ->method('getContentInfo')
                ->willReturn($contentInfo);

            $hits[] = new SearchHit(['valueObject' => $locationMock]);
        }

        return new SearchResult(['searchHits' => $hits]);
    }
}
