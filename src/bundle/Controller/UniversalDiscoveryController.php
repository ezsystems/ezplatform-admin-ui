<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUiBundle\Controller;

use eZ\Publish\API\Repository\BookmarkService;
use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\Repository;
use eZ\Publish\API\Repository\SearchService;
use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\Search\SearchHit;
use eZ\Publish\API\Repository\Values\User\Limitation;
use eZ\Publish\Core\Repository\Values\Content\Content as CoreContent;
use eZ\Publish\Core\Repository\Values\Content\VersionInfo;
use EzSystems\EzPlatformAdminUi\Permission\LookupLimitationsTransformer;
use EzSystems\EzPlatformAdminUi\Permission\PermissionCheckerInterface;
use EzSystems\EzPlatformRest\Output\Visitor;
use EzSystems\EzPlatformRest\Server\Values\Version;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class UniversalDiscoveryController extends Controller
{
    private const ROOT_LOCATION_ID = 1;

    private const SORT_CLAUSE_DATE_PUBLISHED = 'DatePublished';
    private const SORT_CLAUSE_CONTENT_NAME = 'ContentName';

    /** @var \eZ\Publish\API\Repository\Repository */
    private $repository;

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

    private $sortClauseClassMap = [
        self::SORT_CLAUSE_DATE_PUBLISHED => Query\SortClause\DatePublished::class,
        self::SORT_CLAUSE_CONTENT_NAME => Query\SortClause\ContentName::class,
    ];

    private $availableSortOrder = [
        Query::SORT_ASC,
        Query::SORT_DESC,
    ];

    public function __construct(
        Repository $repository,
        LocationService $locationService,
        ContentTypeService $contentTypeService,
        SearchService $searchService,
        BookmarkService $bookmarkService,
        ContentService $contentService,
        Visitor $visitor,
        PermissionCheckerInterface $permissionChecker,
        LookupLimitationsTransformer $lookupLimitationsTransformer
    ) {
        $this->repository = $repository;
        $this->searchService = $searchService;
        $this->contentTypeService = $contentTypeService;
        $this->locationService = $locationService;
        $this->visitor = $visitor;
        $this->bookmarkService = $bookmarkService;
        $this->contentService = $contentService;
        $this->permissionChecker = $permissionChecker;
        $this->lookupLimitationsTransformer = $lookupLimitationsTransformer;
    }

    public function locationAction(
        Request $request,
        int $locationId
    ): JsonResponse {
        $location = $this->loadLocation($locationId);

        $offset = $request->query->getInt('offset', 0);
        $limit = $request->query->getInt('limit', 25);
        $sortClauseName = $request->query->getAlpha('sortClause', self::SORT_CLAUSE_DATE_PUBLISHED);
        $sortOrder = $request->query->getAlpha('sortOrder', Query::SORT_ASC);

        $sortClause = $this->getSortClause($sortClauseName, $sortOrder);

        return new JsonResponse(
            $this->getLocationData($location, $offset, $limit, $sortClause)
        );
    }

    public function locationGridViewAction(
        Request $request,
        int $locationId
    ): JsonResponse {
        $location = $this->loadLocation($locationId);

        $offset = $request->query->getInt('offset', 0);
        $limit = $request->query->getInt('limit', 25);
        $sortClauseName = $request->query->getAlpha('sortClause', self::SORT_CLAUSE_DATE_PUBLISHED);
        $sortOrder = $request->query->getAlpha('sortOrder', Query::SORT_ASC);

        $sortClause = $this->getSortClause($sortClauseName, $sortOrder);

        return new JsonResponse(
            $this->getLocationGridViewData($location, $offset, $limit, $sortClause)
        );
    }

    public function accordionAction(
        Request $request,
        int $locationId
    ): JsonResponse {
        $location = $this->loadLocation($locationId);

        $limit = $request->query->getInt('limit', 25);
        $sortClauseName = $request->query->getAlpha('sortClause', self::SORT_CLAUSE_DATE_PUBLISHED);
        $sortOrder = $request->query->getAlpha('sortOrder', Query::SORT_ASC);

        $sortClause = $this->getSortClause($sortClauseName, $sortOrder);
        $breadcrumbLocations = $this->getBreadcrumbLocations($location);

        $columns = $this->getColumns($location, $limit, $sortClause);

        return new JsonResponse([
            'breadcrumb' => array_map(
                function (Location $location) {
                    return $this->getRestFormat($location);
                },
                $breadcrumbLocations
            ),
            'columns' => $columns,
        ]);
    }

    public function accordionGridViewAction(
        Request $request,
        int $locationId
    ): JsonResponse {
        $location = $this->loadLocation($locationId);

        $limit = $request->query->getInt('limit', 25);
        $sortClauseName = $request->query->getAlpha('sortClause', self::SORT_CLAUSE_DATE_PUBLISHED);
        $sortOrder = $request->query->getAlpha('sortOrder', Query::SORT_ASC);

        $sortClause = $this->getSortClause($sortClauseName, $sortOrder);

        $columns = $this->getColumns($location, $limit, $sortClause, true);

        return new JsonResponse([
            'breadcrumb' => array_map(
                function (Location $location) {
                    return $this->getRestFormat($location);
                },
                $this->getBreadcrumbLocations($location)
            ),
            'columns' => $columns,
        ]);
    }

    private function getColumns(
        Location $location,
        int $limit,
        Query\SortClause $sortClause,
        bool $gridView = false
    ): array {
        $breadcrumbLocations = $this->getBreadcrumbLocations($location);
        $breadcrumbLocationsLast = count($breadcrumbLocations) - 1;

        $columnLocations = $breadcrumbLocationsLast < 2
            ? $breadcrumbLocations
            : [
                $breadcrumbLocations[0], // First
                $breadcrumbLocations[$breadcrumbLocationsLast - 1], // Before last
                $breadcrumbLocations[$breadcrumbLocationsLast], // Last
            ];

        foreach ($columnLocations as $columnLocation) {
            if (!isset($columns[$columnLocation->id])) {
                $columns[$columnLocation->id] = [
                    'location' => $this->getRestFormat($columnLocation),
                    'subitems' => [
                        'locations' => $this->getSubitemLocations($columnLocation, 0, $limit, $sortClause),
                    ],
                ];
            }
        }

        $columns[$location->id] = $gridView
            ? $this->getLocationGridViewData($location, 0, $limit, $sortClause)
            : $this->getLocationData($location, 0, $limit, $sortClause);

        return $columns;
    }

    private function getParentLocationPath(Location $location): array
    {
        $parentPath = array_slice($location->path, 0, -1);

        return array_map(static function ($locationId) {
            return (int)$locationId;
        }, $parentPath);
    }

    private function getBreadcrumbLocations(Location $location): array
    {
        $filter = $location->id === 1
            ? new Query\Criterion\ParentLocationId($location->path)
            : new Query\Criterion\LocationId($this->getParentLocationPath($location));

        $searchResult = $this->searchService->findLocations(
            new LocationQuery([
                'filter' => $filter,
            ])
        );

        return array_map(
            function (SearchHit $searchHit) {
                return $searchHit->valueObject;
            }, $searchResult->searchHits
        );
    }

    private function getLocationPermissionRestrictions(Location $location): array
    {
        $lookupLimitationsResult = $this->permissionChecker->getContentCreateLimitations($location);
        $limitationsValues = $this->lookupLimitationsTransformer->getGroupedLimitationValues(
            $lookupLimitationsResult,
            [Limitation::CONTENTTYPE, Limitation::LANGUAGE]
        );

        return [
            'hasAccess' => $lookupLimitationsResult->hasAccess,
            'restrictedContentTypeIds' => $limitationsValues[Limitation::CONTENTTYPE],
            'restrictedLanguageCodes' => $limitationsValues[Limitation::LANGUAGE],
        ];
    }

    private function getSubitemContents(
        Location $location,
        int $offset,
        int $limit,
        Query\SortClause $sortClause
    ): array {
        $searchResult = $this->searchService->findContent(
            new LocationQuery([
                'filter' => new Query\Criterion\ParentLocationId($location->id),
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

    private function getSubitemLocations(
        Location $location,
        int $offset,
        int $limit,
        Query\SortClause $sortClause
    ): array {
        $searchResult = $this->searchService->findLocations(
            new LocationQuery([
                'filter' => new Query\Criterion\ParentLocationId($location->id),
                'sortClauses' => [$sortClause],
                'offset' => $offset,
                'limit' => $limit,
            ])
        );

        return array_map(
            function (SearchHit $searchHit) {
                return $this->getRestFormat($searchHit->valueObject);
            },
            $searchResult->searchHits
        );
    }

    private function getLocationData(
        Location $location,
        int $offset,
        int $limit,
        Query\SortClause $sortClause
    ): array {
        $content = $location->id === 1
            ? $this->getRootContent($location)
            : $this->contentService->loadContentByContentInfo($location->getContentInfo());
        $contentType = $this->contentTypeService->loadContentType($location->getContentInfo()->contentTypeId);

        return [
            'location' => $this->getRestFormat($location),
            'bookmark' => $this->bookmarkService->isBookmarked($location),
            'permissions' => $this->getLocationPermissionRestrictions($location),
            'version' => $this->getRestFormat(new Version($content, $contentType, [])),
            'subitems' => [
                'locations' => $this->getSubitemLocations($location, $offset, $limit, $sortClause),
            ],
        ];
    }

    private function getLocationGridViewData(
        Location $location,
        int $offset,
        int $limit,
        Query\SortClause $sortClause
    ): array {
        $content = $location->id === 1
            ? $this->getRootContent($location)
            : $this->contentService->loadContentByContentInfo($location->getContentInfo());
        $contentType = $this->contentTypeService->loadContentType($location->getContentInfo()->contentTypeId);

        return [
            'location' => $this->getRestFormat($location),
            'bookmark' => $this->bookmarkService->isBookmarked($location),
            'permissions' => $this->getLocationPermissionRestrictions($location),
            'version' => $this->getRestFormat(new Version($content, $contentType, [])),
            'subitems' => [
                'locations' => $this->getSubitemLocations($location, $offset, $limit, $sortClause),
                'versions' => $this->getSubitemContents($location, $offset, $limit, $sortClause),
            ],
        ];
    }

    private function getRestFormat($valueObject): array
    {
        return json_decode(
            $this->visitor->visit($valueObject)->getContent(),
            true
        );
    }

    private function getSortClause(string $sortClauseName, string $sortOrder): Query\SortClause
    {
        $sortClauseClass = $this->sortClauseClassMap[$sortClauseName] ?? $this->sortClauseClassMap[self::SORT_CLAUSE_DATE_PUBLISHED];
        $sortOrder = !in_array($sortOrder, $this->availableSortOrder)
            ? $this->availableSortOrder[0]
            : $sortOrder;

        return new $sortClauseClass($sortOrder);
    }

    private function getRootContent(Location $location): Content
    {
        $contentInfo = $location->getContentInfo();

        return new CoreContent([
            'versionInfo' => new VersionInfo([
                'names' => [
                    $contentInfo->mainLanguageCode => $contentInfo->name,
                ],
                'contentInfo' => $contentInfo,
                'versionNo' => $contentInfo->currentVersionNo,
                'modificationDate' => $contentInfo->modificationDate,
                'creationDate' => $contentInfo->modificationDate,
                'creatorId' => $contentInfo->ownerId,
            ]),
        ]);
    }

    private function loadLocation(int $locationId): Location
    {
        if ($locationId === self::ROOT_LOCATION_ID) {
            return $this->repository->sudo(function (Repository $repository) use ($locationId) {
                return $repository->getLocationService()->loadLocation($locationId);
            });
        }

        return $this->locationService->loadLocation($locationId);
    }
}
