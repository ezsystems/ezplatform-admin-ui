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
use eZ\Publish\API\Repository\SearchService;
use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\Search\SearchHit;
use eZ\Publish\API\Repository\Values\User\Limitation;
use EzSystems\EzPlatformAdminUi\Permission\LookupLimitationsTransformer;
use EzSystems\EzPlatformAdminUi\Permission\PermissionCheckerInterface;
use EzSystems\EzPlatformRest\Output\Visitor;
use EzSystems\EzPlatformRest\Server\Values\Version;
use Symfony\Component\HttpFoundation\JsonResponse;

class UniversalDiscoveryController extends Controller
{
    /** @var \eZ\Publish\API\Repository\LocationService */
    protected $locationService;

    /** @var \eZ\Publish\API\Repository\ContentTypeService */
    protected $contentTypeService;

    /** @var \eZ\Publish\API\Repository\SearchService */
    protected $searchService;

    /** @var \EzSystems\EzPlatformRest\Output\Visitor */
    protected $visitor;

    /** @var \eZ\Publish\API\Repository\BookmarkService */
    private $bookmarkService;

    /** @var \eZ\Publish\API\Repository\ContentService */
    private $contentService;

    /** @var \EzSystems\EzPlatformAdminUi\Permission\PermissionCheckerInterface */
    private $permissionChecker;

    /** @var \EzSystems\EzPlatformAdminUi\Permission\LookupLimitationsTransformer */
    private $lookupLimitationsTransformer;

    public function __construct(
        LocationService $locationService,
        ContentTypeService $contentTypeService,
        SearchService $searchService,
        BookmarkService $bookmarkService,
        ContentService $contentService,
        Visitor $visitor,
        PermissionCheckerInterface $permissionChecker,
        LookupLimitationsTransformer $lookupLimitationsTransformer
    ) {
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
        Location $location
    ): JsonResponse {
        return new JsonResponse(
            $this->getLocationData($location)
        );
    }

    public function locationGridViewAction(
        Location $location
    ): JsonResponse {
        return new JsonResponse(
            $this->getLocationGridViewData($location)
        );
    }

    public function getParentLocationPath(Location $location): array
    {
        $parentPath = array_slice($location->path, 0, -1);

        return array_map(static function ($locationId) {
            return (int)$locationId;
        }, $parentPath);
    }

    public function accordionAction(
        Location $location
    ): JsonResponse {
        $breadcrumbLocations = $this->getBreadcrumbLocations($location);

        $columnLocations = array_filter([
            reset($breadcrumbLocations),
            end($breadcrumbLocations),
        ]);

        foreach ($columnLocations as $columnLocation) {
            if (!isset($columns[$columnLocation->id])) {
                $columns[$columnLocation->id] = [
                    'location' => $this->getRestFormat($columnLocation),
                    'subitems' => [
                        'locations' => $this->getSubitemLocations($columnLocation),
                    ],
                ];
            }
        }

        $columns[$location->id] = $this->getLocationData($location);

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
        Location $location
    ): JsonResponse {
        return new JsonResponse([
            'breadcrumb' => array_map(
                function (Location $location) {
                    return $this->getRestFormat($location);
                },
                $this->getBreadcrumbLocations($location)
            ),
            'columns' => [
                $location->id => $this->getLocationGridViewData($location),
            ],
        ]);
    }

    private function getBreadcrumbLocations(Location $location): array
    {
        $searchResult = $this->searchService->findLocations(
            new LocationQuery([
                'filter' => new Query\Criterion\LocationId($this->getParentLocationPath($location)),
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

    private function getSubitemContents(Location $location): array
    {
        $searchResult = $this->searchService->findContent(
            new LocationQuery([
                'filter' => new Query\Criterion\ParentLocationId($location->id),
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

    private function getSubitemLocations(Location $location): array
    {
        $searchResult = $this->searchService->findLocations(
            new LocationQuery([
                'filter' => new Query\Criterion\ParentLocationId($location->id),
            ])
        );

        return array_map(
            function (SearchHit $searchHit) {
                return $this->getRestFormat($searchHit->valueObject);
            },
            $searchResult->searchHits
        );
    }

    private function getLocationData(Location $location): array
    {
        $content = $this->contentService->loadContentByContentInfo($location->getContentInfo());
        $contentType = $this->contentTypeService->loadContentType($location->getContentInfo()->contentTypeId);

        return [
            'location' => $this->getRestFormat($location),
            'bookmark' => $this->bookmarkService->isBookmarked($location),
            'permissions' => $this->getLocationPermissionRestrictions($location),
            'version' => $this->getRestFormat(new Version($content, $contentType, [])),
            'subitems' => [
                'locations' => $this->getSubitemLocations($location),
            ],
        ];
    }

    private function getLocationGridViewData(Location $location): array
    {
        $content = $this->contentService->loadContentByContentInfo($location->getContentInfo());
        $contentType = $this->contentTypeService->loadContentType($location->getContentInfo()->contentTypeId);

        return [
            'location' => $this->getRestFormat($location),
            'bookmark' => $this->bookmarkService->isBookmarked($location),
            'permissions' => $this->getLocationPermissionRestrictions($location),
            'version' => $this->getRestFormat(new Version($content, $contentType, [])),
            'subitems' => [
                'locations' => $this->getSubitemLocations($location),
                'versions' => $this->getSubitemContents($location),
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
}
