<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\UniversalDiscovery;

use eZ\Publish\API\Repository\BookmarkService;
use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\SearchService;
use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\Search\SearchHit;
use eZ\Publish\API\Repository\Values\User\Limitation;
use EzSystems\EzPlatformAdminUi\Permission\LookupLimitationsTransformer;
use EzSystems\EzPlatformAdminUi\Permission\PermissionCheckerInterface;
use EzSystems\EzPlatformAdminUi\QueryType\LocationPathQueryType;
use EzSystems\EzPlatformRest\Output\Visitor;
use EzSystems\EzPlatformRest\Server\Values\Version;

class UniversalDiscoveryProvider implements Provider
{
    private const COLUMNS_NUMBER = 4;

    /** @var \eZ\Publish\API\Repository\LocationService */
    private $locationService;

    /** @var \eZ\Publish\API\Repository\ContentTypeService */
    private $contentTypeService;

    /** @var \eZ\Publish\API\Repository\SearchService */
    private $searchService;

    /** @var \EzSystems\EzPlatformRest\Output\Visitor */
    private $visitor;

    /** @var \eZ\Publish\API\Repository\BookmarkService */
    private $bookmarkService;

    /** @var \eZ\Publish\API\Repository\ContentService */
    private $contentService;

    /** @var \EzSystems\EzPlatformAdminUi\Permission\PermissionCheckerInterface */
    private $permissionChecker;

    /** @var \EzSystems\EzPlatformAdminUi\Permission\LookupLimitationsTransformer */
    private $lookupLimitationsTransformer;

    /** @var \EzSystems\EzPlatformAdminUi\QueryType\LocationPathQueryType */
    private $locationPathQueryType;

    private $sortClauseClassMap = [
        self::SORT_CLAUSE_DATE_PUBLISHED => Query\SortClause\DatePublished::class,
        self::SORT_CLAUSE_CONTENT_NAME => Query\SortClause\ContentName::class,
    ];

    private $availableSortOrder = [
        Query::SORT_ASC,
        Query::SORT_DESC,
    ];

    public function __construct(
        LocationService $locationService,
        ContentTypeService $contentTypeService,
        SearchService $searchService,
        BookmarkService $bookmarkService,
        ContentService $contentService,
        Visitor $visitor,
        PermissionCheckerInterface $permissionChecker,
        LookupLimitationsTransformer $lookupLimitationsTransformer,
        LocationPathQueryType $locationPathQueryType
    ) {
        $this->locationService = $locationService;
        $this->contentTypeService = $contentTypeService;
        $this->searchService = $searchService;
        $this->bookmarkService = $bookmarkService;
        $this->contentService = $contentService;
        $this->visitor = $visitor;
        $this->permissionChecker = $permissionChecker;
        $this->lookupLimitationsTransformer = $lookupLimitationsTransformer;
        $this->locationPathQueryType = $locationPathQueryType;
    }

    public function getColumns(
        int $locationId,
        int $limit,
        Query\SortClause $sortClause,
        bool $gridView = false,
        int $rootLocationId = Provider::ROOT_LOCATION_ID
    ): array {
        $location = $this->locationService->loadLocation($locationId);

        $locationPath = $this->getRelativeLocationPath($rootLocationId, $location->path);
        $locationPathCount = count($locationPath);

        $locationPathLast = $locationPathCount - 1;
        if ($locationPathCount > self::COLUMNS_NUMBER) {
            $columnLocations = [
                $locationPath[0], // First
                $locationPath[$locationPathLast - 2],
                $locationPath[$locationPathLast - 1],
                $locationPath[$locationPathLast], // Last
            ];
        } else {
            $columnLocations = $locationPath;
        }

        $locationPathIndex = $locationPathCount > 0 ? $locationPathLast - 1 : 0;
        $lastColumnLocationId = (int) $locationPath[$locationPathIndex];

        $columns = [];
        foreach ($columnLocations as $columnLocationId) {
            $columnLocationId = (int)$columnLocationId;
            $columnLocation = ($columnLocationId !== self::ROOT_LOCATION_ID)
                ? $this->getRestFormat($this->locationService->loadLocation($columnLocationId))
                : null;

            $subItems = $this->getSubitemLocations($columnLocationId, 0, $limit, $sortClause);
            $isLastColumnLocationId = $columnLocationId === $lastColumnLocationId;
            $locations = $this->moveSelectedLocationOnTop($location, $subItems['locations'], $isLastColumnLocationId);

            $subItems['locations'] = $locations;

            $columns[$columnLocationId] = [
                'location' => $columnLocation,
                'subitems' => $subItems,
            ];
        }

        $columns[$locationId] = $gridView
            ? $this->getLocationGridViewData($locationId, 0, $limit, $sortClause)
            : $this->getLocationData($locationId, 0, $limit, $sortClause);

        return $columns;
    }

    public function getBreadcrumbLocations(
        int $locationId,
        int $rootLocationId = self::ROOT_LOCATION_ID
    ): array {
        $searchResult = $this->searchService->findLocations(
            $this->locationPathQueryType->getQuery([
                'location' => $this->locationService->loadLocation($locationId),
                'rootLocationId' => $rootLocationId,
            ])
        );

        return array_map(
            static function (SearchHit $searchHit) {
                return $searchHit->valueObject;
            }, $searchResult->searchHits
        );
    }

    public function getLocations(array $locationIds): array
    {
        $searchResult = $this->searchService->findLocations(
            new LocationQuery([
                'filter' => new Query\Criterion\LocationId($locationIds),
            ])
        );

        return array_map(
            function (SearchHit $searchHit) {
                /** @var \eZ\Publish\API\Repository\Values\Content\Location $location */
                $location = $searchHit->valueObject;

                return [
                    'location' => $this->getRestFormat($location),
                    'permissions' => $this->getLocationPermissionRestrictions($location),
                ];
            },
            $searchResult->searchHits
        );
    }

    public function getLocationPermissionRestrictions(Location $location): array
    {
        $lookupCreateLimitationsResult = $this->permissionChecker->getContentCreateLimitations($location);
        $lookupUpdateLimitationsResult = $this->permissionChecker->getContentUpdateLimitations($location);

        $createLimitationsValues = $this->lookupLimitationsTransformer->getGroupedLimitationValues(
            $lookupCreateLimitationsResult,
            [Limitation::CONTENTTYPE, Limitation::LANGUAGE]
        );

        return [
            'create' => [
                'hasAccess' => $lookupCreateLimitationsResult->hasAccess,
                'restrictedContentTypeIds' => $createLimitationsValues[Limitation::CONTENTTYPE],
                'restrictedLanguageCodes' => $createLimitationsValues[Limitation::LANGUAGE],
            ],
            'edit' => [
                'hasAccess' => $lookupUpdateLimitationsResult->hasAccess,
            ],
        ];
    }

    public function getSubitemContents(
        int $locationId,
        int $offset,
        int $limit,
        Query\SortClause $sortClause
    ): array {
        $searchResult = $this->searchService->findContent(
            new LocationQuery([
                'filter' => new Query\Criterion\ParentLocationId($locationId),
                'sortClauses' => [$sortClause],
                'offset' => $offset,
                'limit' => $limit,
            ])
        );

        return array_map(
            function (SearchHit $searchHit) {
                /** @var \eZ\Publish\API\Repository\Values\Content\Content $content */
                $content = $searchHit->valueObject;

                return $this->getRestFormat(
                    new Version($content, $content->getContentType(), [])
                );
            },
            $searchResult->searchHits
        );
    }

    public function getSubitemLocations(
        int $locationId,
        int $offset,
        int $limit,
        Query\SortClause $sortClause
    ): array {
        $searchResult = $this->searchService->findLocations(
            new LocationQuery([
                'filter' => new Query\Criterion\ParentLocationId($locationId),
                'sortClauses' => [$sortClause],
                'offset' => $offset,
                'limit' => $limit,
            ])
        );

        return [
            'locations' => array_map(
                function (SearchHit $searchHit) {
                    return $this->getRestFormat($searchHit->valueObject);
                },
                $searchResult->searchHits
            ),
            'totalCount' => $searchResult->totalCount,
        ];
    }

    public function getLocationData(
        int $locationId,
        int $offset,
        int $limit,
        Query\SortClause $sortClause
    ): array {
        if ($locationId === self::ROOT_LOCATION_ID) {
            return [
                'subitems' => $this->getSubitemLocations($locationId, $offset, $limit, $sortClause),
            ];
        }

        $location = $this->locationService->loadLocation($locationId);
        $content = $this->contentService->loadContentByContentInfo($location->getContentInfo());
        $contentType = $this->contentTypeService->loadContentType($location->getContentInfo()->contentTypeId);

        return [
            'location' => $this->getRestFormat($location),
            'bookmarked' => $this->bookmarkService->isBookmarked($location),
            'permissions' => $this->getLocationPermissionRestrictions($location),
            'version' => $this->getRestFormat(new Version($content, $contentType, [])),
            'subitems' => $this->getSubitemLocations($locationId, $offset, $limit, $sortClause),
        ];
    }

    public function getLocationGridViewData(
        int $locationId,
        int $offset,
        int $limit,
        Query\SortClause $sortClause
    ): array {
        if ($locationId === self::ROOT_LOCATION_ID) {
            $locations = $this->getSubitemLocations($locationId, $offset, $limit, $sortClause);
            $versions = $this->getSubitemContents($locationId, $offset, $limit, $sortClause);

            return [
                'subitems' => [
                    'locations' => $locations['locations'],
                    'totalCount' => $locations['totalCount'],
                    'versions' => $versions,
                ],
            ];
        }

        $location = $this->locationService->loadLocation($locationId);
        $content = $this->contentService->loadContentByContentInfo($location->getContentInfo());
        $contentType = $this->contentTypeService->loadContentType($location->getContentInfo()->contentTypeId);

        $locations = $this->getSubitemLocations($locationId, $offset, $limit, $sortClause);
        $versions = $this->getSubitemContents($locationId, $offset, $limit, $sortClause);

        return [
            'location' => $this->getRestFormat($location),
            'bookmarked' => $this->bookmarkService->isBookmarked($location),
            'permissions' => $this->getLocationPermissionRestrictions($location),
            'version' => $this->getRestFormat(new Version($content, $contentType, [])),
            'subitems' => [
                'locations' => $locations['locations'],
                'totalCount' => $locations['totalCount'],
                'versions' => $versions,
            ],
        ];
    }

    public function getRestFormat($valueObject): array
    {
        return json_decode(
            $this->visitor->visit($valueObject)->getContent(),
            true
        );
    }

    public function getSortClause(string $sortClauseName, string $sortOrder): Query\SortClause
    {
        $sortClauseClass = $this->sortClauseClassMap[$sortClauseName] ?? $this->sortClauseClassMap[self::SORT_CLAUSE_DATE_PUBLISHED];
        $sortOrder = !in_array($sortOrder, $this->availableSortOrder)
            ? Query::SORT_ASC
            : $sortOrder;

        return new $sortClauseClass($sortOrder);
    }

    private function getRelativeLocationPath(int $locationId, array $locationPath): array
    {
        $locationIds = array_values($locationPath);

        $index = array_search($locationId, $locationIds);

        // Location is not part of path
        if ($index === false) {
            return [];
        }

        return array_slice($locationIds, $index);
    }

    private function moveSelectedLocationOnTop(Location $location, array $locations, bool $isLastColumnLocationId): array
    {
        $index = array_search($location->id, array_map(static function (array $location) {
            return $location['Location']['id'];
        }, $locations));

        // Location is on the list, remove because we add location on top
        if ($index !== false) {
            unset($locations[$index]);
            $locations = array_values($locations);
        }

        if ($isLastColumnLocationId) {
            array_unshift($locations, $this->getRestFormat($location));
        }

        return $locations;
    }
}
