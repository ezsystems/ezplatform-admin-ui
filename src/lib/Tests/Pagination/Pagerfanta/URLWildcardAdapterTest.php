<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Tests\Pagination\Pagerfanta;

use eZ\Publish\API\Repository\URLWildcardService;
use eZ\Publish\API\Repository\Values\Content\URLWildcard;
use Ibexa\AdminUi\Pagination\Pagerfanta\URLWildcardAdapter;
use Ibexa\Contracts\Core\Repository\Values\Content\URLWildcard\Query\Criterion;
use Ibexa\Contracts\Core\Repository\Values\Content\URLWildcard\Query\SortClause;
use Ibexa\Contracts\Core\Repository\Values\Content\URLWildcard\SearchResult;
use Ibexa\Contracts\Core\Repository\Values\Content\URLWildcard\URLWildcardQuery;
use PHPUnit\Framework\TestCase;

final class URLWildcardAdapterTest extends TestCase
{
    /** @var \eZ\Publish\API\Repository\Values\Content\URLWildcard|\PHPUnit\Framework\MockObject\MockObject */
    private $urlWildcardService;

    protected function setUp(): void
    {
        $this->urlWildcardService = $this->createMock(URLWildcardService::class);
    }

    public function testGetNbResults(): void
    {
        $query = $this->createURLWildcardQuery();

        $searchResults = new SearchResult([
            'items' => [],
            'totalCount' => 5,
        ]);

        $this->urlWildcardService
            ->expects($this->once())
            ->method('findUrlWildcards')
            ->willReturnCallback(function (URLWildcardQuery $q) use ($query, $searchResults) {
                $this->assertEquals($query->filter, $q->filter);
                $this->assertEquals($query->sortClauses, $q->sortClauses);
                $this->assertEquals(0, $q->offset);
                $this->assertEquals(0, $q->limit);

                return $searchResults;
            });

        $adapter = new URLWildcardAdapter($query, $this->urlWildcardService);

        $this->assertEquals($searchResults->totalCount, $adapter->getNbResults());
    }

    public function testGetSlice(): void
    {
        $query = $this->createURLWildcardQuery();
        $offset = 10;
        $limit = 25;

        $searchResults = new SearchResult([
            'items' => $this->urlWildcards(),
            'totalCount' => 5,
        ]);

        $this->urlWildcardService
            ->expects($this->once())
            ->method('findUrlWildcards')
            ->willReturnCallback(function (URLWildcardQuery $q) use ($query, $limit, $offset, $searchResults) {
                $this->assertEquals($query->filter, $q->filter);
                $this->assertEquals($query->sortClauses, $q->sortClauses);
                $this->assertEquals($limit, $q->limit);
                $this->assertEquals($offset, $q->offset);

                return $searchResults;
            });

        $adapter = new URLWildcardAdapter($query, $this->urlWildcardService);

        $this->assertEquals($searchResults->items, $adapter->getSlice($offset, $limit));
    }

    /**
     * @return  \eZ\Publish\API\Repository\Values\Content\URLWildcard[]
     */
    public function urlWildcards(): array
    {
        return [
            new URLWildcard([
                'id' => 1,
                'destinationUrl' => 'test',
                'sourceUrl' => '/',
                'forward' => true,
            ]),

            new URLWildcard([
                'id' => 2,
                'destinationUrl' => 'test2',
                'sourceUrl' => '/test',
                'forward' => false,
            ]),
        ];
    }

    private function createURLWildcardQuery(): URLWildcardQuery
    {
        $query = new URLWildcardQuery();
        $query->filter = new Criterion\MatchAll();
        $query->sortClauses = [
            new SortClause\Id(),
        ];

        return $query;
    }
}
