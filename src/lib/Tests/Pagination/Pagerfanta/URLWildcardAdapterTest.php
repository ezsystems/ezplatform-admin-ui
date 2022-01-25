<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Tests\Pagination\Pagerfanta;

use eZ\Publish\API\Repository\URLWildcardService;
use Ibexa\AdminUi\Pagination\Pagerfanta\URLWildcardAdapter;
use PHPUnit\Framework\TestCase;
use eZ\Publish\API\Repository\Values\Content\UrlWildcard;

class URLWildcardAdapterTest extends TestCase
{
    /** @var \eZ\Publish\API\Repository\Values\Content\UrlWildcard|\PHPUnit\Framework\MockObject\MockObject */
    private $urlWildcardService;

    protected function setUp(): void
    {
        $this->urlWildcardService = $this->createMock(URLWildcardService::class);
    }

    public function testGetNbResults()
    {
        $countAll = 5;

        $this->urlWildcardService
            ->expects($this->once())
            ->method('countAll')
            ->with()
            ->willReturn($countAll);

        $adapter = new URLWildcardAdapter($this->urlWildcardService);

        $this->assertEquals(
            $countAll,
            $adapter->getNbResults()
        );
    }

    public function testGetSlice()
    {
        $offset = 10;
        $limit = 25;

        $this->urlWildcardService
            ->expects($this->once())
            ->method('loadAll')
            ->with($offset, $limit)
            ->willReturn([$this->urlWildcards()]);

        $adapter = new URLWildcardAdapter($this->urlWildcardService);

        $this->assertEquals(
            [$this->urlWildcards()],
            $adapter->getSlice($offset, $limit)
        );
    }

    public function urlWildcards(): iterable
    {
        yield [
            new UrlWildcard([
                'id' => 1,
                'destinationUrl' => 'test',
                'sourceUrl' => '/',
                'forward' => true,
            ])
        ];

        yield [
            new UrlWildcard([
                'id' => 2,
                'destinationUrl' => 'test2',
                'sourceUrl' => '/test',
                'forward' => false,
            ])
        ];
    }
}
