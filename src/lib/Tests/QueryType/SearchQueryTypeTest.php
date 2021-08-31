<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Tests\QueryType;

use eZ\Publish\API\Repository\SearchService;
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use eZ\Publish\API\Repository\Values\Content\Query\SortClause;
use eZ\Publish\API\Repository\Values\Content\Section;
use eZ\Publish\API\Repository\Values\User\User;
use eZ\Publish\Core\Repository\Values\ContentType\ContentType;
use EzSystems\EzPlatformAdminUi\Form\Data\Search\SearchData;
use EzSystems\EzPlatformAdminUi\QueryType\SearchQueryType;
use PHPUnit\Framework\TestCase;

final class SearchQueryTypeTest extends TestCase
{
    private const EXPECTED_QUERY_STRING = 'eZ Platform';
    private const EXPECTED_SECTION_ID = 2;
    private const EXPECTED_CONTENT_TYPE_IDS = [3, 5, 7];
    private const EXPECTED_USER_ID = 11;
    private const EXPECTED_SUBTREE = '/13/17/19/';
    private const EXPECTED_DATE_RANGE = [1431993600, 1587340800];

    /** @var \eZ\Publish\API\Repository\SearchService|\PHPUnit\Framework\MockObject\MockObject */
    private $searchService;

    /** @var \EzSystems\EzPlatformAdminUi\QueryType\SearchQueryType */
    private $queryType;

    protected function setUp(): void
    {
        $this->searchService = $this->createMock(SearchService::class);
        $this->queryType = new SearchQueryType($this->searchService);
    }

    /**
     * @dataProvider dataProviderForGetQuery
     */
    public function testGetQuery(array $parameters, Query $expectedQuery, bool $isScoringSupported): void
    {
        $this->searchService
            ->method('supports')
            ->with(SearchService::CAPABILITY_SCORING)
            ->willReturn($isScoringSupported);

        $this->assertEquals($expectedQuery, $this->queryType->getQuery($parameters));
    }

    public function dataProviderForGetQuery(): array
    {
        return [
            [
                [],
                new Query(['sortClauses' => []]),
                true,
            ],
            [
                [
                    'search_data' => $this->createSearchDataWithAllCriteria(),
                ],
                $this->createExpectedQueryForAllCriteria([]),
                true,
            ],
            [
                [],
                new Query([
                    'sortClauses' => [
                        new SortClause\DateModified(),
                    ],
                ]),
                false,
            ],
            [
                [
                    'search_data' => $this->createSearchDataWithAllCriteria(),
                ],
                $this->createExpectedQueryForAllCriteria(),
                false,
            ],
        ];
    }

    private function createContentTypesList(array $ids): array
    {
        return array_map(static function (int $id): ContentType {
            return new ContentType(['id' => $id]);
        }, $ids);
    }

    private function createSearchDataWithAllCriteria(): SearchData
    {
        $searchData = new SearchData();
        $searchData->setQuery(self::EXPECTED_QUERY_STRING);
        $searchData->setSection(new Section(['id' => self::EXPECTED_SECTION_ID]));
        $searchData->setContentTypes($this->createContentTypesList(self::EXPECTED_CONTENT_TYPE_IDS));
        $searchData->setCreated([
            'start_date' => self::EXPECTED_DATE_RANGE[0],
            'end_date' => self::EXPECTED_DATE_RANGE[1],
        ]);
        $searchData->setLastModified([
            'start_date' => self::EXPECTED_DATE_RANGE[0],
            'end_date' => self::EXPECTED_DATE_RANGE[1],
        ]);
        $searchData->setCreator($this->createUser(self::EXPECTED_USER_ID));
        $searchData->setSubtree(self::EXPECTED_SUBTREE);

        return $searchData;
    }

    private function createUser(int $id): User
    {
        $user = $this->createMock(User::class);
        $user->method('__get')->with('id')->willReturn($id);

        return $user;
    }

    private function createExpectedQueryForAllCriteria(?array $expectedSortClauses = null): Query
    {
        if ($expectedSortClauses === null) {
            $expectedSortClauses = [
                new SortClause\DateModified(),
            ];
        }

        return new Query([
            'query' => new Criterion\FullText(self::EXPECTED_QUERY_STRING),
            'filter' => new Criterion\LogicalAnd([
                new Criterion\SectionId(self::EXPECTED_SECTION_ID),
                new Criterion\ContentTypeId(self::EXPECTED_CONTENT_TYPE_IDS),
                new Criterion\DateMetadata(
                    Criterion\DateMetadata::MODIFIED,
                    Criterion\Operator::BETWEEN,
                    self::EXPECTED_DATE_RANGE
                ),
                new Criterion\DateMetadata(
                    Criterion\DateMetadata::CREATED,
                    Criterion\Operator::BETWEEN,
                    self::EXPECTED_DATE_RANGE
                ),
                new Criterion\UserMetadata(
                    Criterion\UserMetadata::OWNER,
                    Criterion\Operator::EQ,
                    self::EXPECTED_USER_ID
                ),
                new Criterion\Subtree(self::EXPECTED_SUBTREE),
            ]),
            'sortClauses' => $expectedSortClauses,
        ]);
    }
}
