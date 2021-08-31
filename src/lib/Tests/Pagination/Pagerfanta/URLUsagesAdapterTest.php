<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Tests\Pagination\Pagerfanta;

use eZ\Publish\API\Repository\URLService;
use eZ\Publish\API\Repository\Values\Content\Search\SearchHit;
use eZ\Publish\API\Repository\Values\URL\URL;
use eZ\Publish\API\Repository\Values\URL\UsageSearchResult;
use EzSystems\EzPlatformAdminUi\Pagination\Pagerfanta\URLUsagesAdapter;
use PHPUnit\Framework\TestCase;

class URLUsagesAdapterTest extends TestCase
{
    /** @var \eZ\Publish\API\Repository\URLService|\PHPUnit\Framework\MockObject\MockObject */
    private $urlService;

    protected function setUp(): void
    {
        $this->urlService = $this->createMock(URLService::class);
    }

    public function testGetNbResults()
    {
        $url = $this->createMock(URL::class);

        $searchResults = new UsageSearchResult([
            'items' => [],
            'totalCount' => 10,
        ]);

        $this->urlService
            ->expects($this->once())
            ->method('findUsages')
            ->with($url, 0, 0)
            ->willReturn($searchResults);

        $adapter = new URLUsagesAdapter($url, $this->urlService);

        $this->assertEquals(
            $searchResults->totalCount,
            $adapter->getNbResults()
        );
    }

    public function testGetSlice()
    {
        $url = $this->createMock(URL::class);
        $offset = 10;
        $limit = 25;

        $searchResults = new UsageSearchResult([
            'items' => [
                $this->createMock(SearchHit::class),
                $this->createMock(SearchHit::class),
                $this->createMock(SearchHit::class),
            ],
            'totalCount' => 13,
        ]);

        $this->urlService
            ->expects($this->once())
            ->method('findUsages')
            ->with($url, $offset, $limit)
            ->willReturn($searchResults);

        $adapter = new URLUsagesAdapter($url, $this->urlService);

        $this->assertEquals(
            $searchResults->items,
            $adapter->getSlice($offset, $limit)
        );
    }
}
