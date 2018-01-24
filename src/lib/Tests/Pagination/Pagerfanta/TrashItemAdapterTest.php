<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Tests\Pagination\Pagerfanta;

use eZ\Publish\API\Repository\TrashService;
use EzSystems\EzPlatformAdminUi\Pagination\Pagerfanta\TrashItemAdapter;
use PHPUnit\Framework\TestCase;
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\Query\CriterionInterface;
use eZ\Publish\API\Repository\Values\Content\Query\SortClause;
use eZ\Publish\API\Repository\Values\Content\Trash\SearchResult;
use eZ\Publish\API\Repository\Values\Content\Content as APIContent;

class TrashItemAdapterTest extends TestCase
{
    /**
     * @var \eZ\Publish\API\Repository\TrashService|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $trashService;

    protected function setUp()
    {
        parent::setUp();
        $this->trashService = $this->createMock(TrashService::class);
    }

    /**
     * Returns the adapter to test.
     *
     * @param Query $query
     * @param TrashService $trashService
     *
     * @return TrashItemAdapter
     */
    protected function getAdapter(Query $query, TrashService $trashService): TrashItemAdapter
    {
        return new TrashItemAdapter($query, $trashService);
    }

    public function testGetNbResults()
    {
        $nbResults = 123;
        $query = new Query();
        $query->query = $this->createMock(CriterionInterface::class);
        $query->sortClauses = $this->getMockBuilder(SortClause::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        // Count query will necessarily have a 0 limit.
        $countQuery = clone $query;
        $countQuery->limit = 0;

        $searchResult = new SearchResult(['count' => $nbResults]);
        $this->trashService
            ->expects($this->once())
            ->method('findTrashItems')
            ->with($this->equalTo($countQuery))
            ->will($this->returnValue($searchResult));

        $adapter = $this->getAdapter($query, $this->trashService);
        $this->assertSame($nbResults, $adapter->getNbResults());

        // Running a 2nd time to ensure SearchService::findContent() is called only once.
        $this->assertSame($nbResults, $adapter->getNbResults());
    }

    public function testGetSlice()
    {
        $offset = 20;
        $limit = 25;
        $nbResults = 123;

        $query = new Query();
        $query->query = $this->createMock(CriterionInterface::class);
        $query->sortClauses = $this->getMockBuilder(SortClause::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        // Injected query is being cloned to modify offset/limit,
        // so we need to do the same here for our assertions.
        $searchQuery = clone $query;
        $searchQuery->offset = $offset;
        $searchQuery->limit = $limit;
        $searchQuery->performCount = false;

        $items = [];
        for ($i = 0; $i < $limit; ++$i) {
            $content = $this->getMockForAbstractClass(APIContent::class);
            $items[] = $content;
        }

        $searchResult = new SearchResult(['items' => $items, 'count' => $nbResults]);

        $this->trashService
            ->expects($this->once())
            ->method('findTrashItems')
            ->with($this->equalTo($searchQuery))
            ->will($this->returnValue($searchResult));

        $adapter = $this->getAdapter($query, $this->trashService);

        $this->assertSame($items, $adapter->getSlice($offset, $limit));
        $this->assertSame($nbResults, $adapter->getNbResults());
        // Running a 2nd time to ensure SearchService::findContent() is called only once.
        $this->assertSame($nbResults, $adapter->getNbResults());
    }
}
