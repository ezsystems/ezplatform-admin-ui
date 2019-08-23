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
use eZ\Publish\API\Repository\Values\ValueObject;
use EzSystems\EzPlatformRest\Output\Visitor;
use EzSystems\EzPlatformRest\Server\Values\Version;
use Symfony\Component\HttpFoundation\JsonResponse;

class UniversalDiscoveryController extends Controller
{
    /** @var \eZ\Publish\API\Repository\LocationService */
    protected $locationService;

    protected $contentTypeService;

    /** @var \eZ\Publish\API\Repository\SearchService */
    protected $searchService;

    /** @var \EzSystems\EzPlatformRest\Output\Visitor */
    protected $visitor;

    /** @var \eZ\Publish\API\Repository\BookmarkService */
    private $bookmarkService;
    /**
     * @var \eZ\Publish\API\Repository\ContentService
     */
    private $contentService;

    public function __construct(
        LocationService $locationService,
        ContentTypeService $contentTypeService,
        SearchService $searchService,
        BookmarkService $bookmarkService,
        ContentService $contentService,
        Visitor $visitor
    ) {
        $this->searchService = $searchService;
        $this->contentTypeService = $contentTypeService;
        $this->locationService = $locationService;
        $this->visitor = $visitor;
        $this->bookmarkService = $bookmarkService;
        $this->contentService = $contentService;
    }

    public function browseLocationAction(
        Location $location
    ): JsonResponse {
        $subitems = $this->searchService->findLocations(
            new LocationQuery([
                'filter' => new Query\Criterion\ParentLocationId($location->id),
            ])
        );
        $content = $this->contentService->loadContentByContentInfo($location->getContentInfo());
        $contentType = $this->contentTypeService->loadContentType($location->getContentInfo()->contentTypeId);

        return new JsonResponse([
            'location' => $this->getRestFormat($location),
            'bookmark' => $this->bookmarkService->isBookmarked($location),
            'version' => $this->getRestFormat(new Version(
                $content,
                $contentType,
                [],
            )),
            'subitems' => [
                'locations' => array_map(function (SearchHit $searchHit) {
                    return $this->getRestFormat($searchHit->valueObject);
                }, $subitems->searchHits),
                'totalCount' => $subitems->totalCount,
            ],
        ]);
    }

    /**
     * @param int $startingLocationId
     * @param int $locationId
     * @param int $limit
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
    public function preselectedLocationDataAction(int $startingLocationId, int $locationId, int $limit = 50): JsonResponse
    {
        $location = $this->locationService->loadLocation($locationId);

        $subitemsLocationIds = array_unique([
            $startingLocationId,
            $location->parentLocationId,
            $location->id,
        ]);

        $relativeLocationPath = array_slice(
            $location->path,
            array_search($startingLocationId, $location->path)
        );

        $locations = $this->searchService->findLocations(
            new LocationQuery([
                'filter' => new Query\Criterion\LocationId($relativeLocationPath),
            ])
        );

        $result = [];

        foreach ($locations->searchHits as $location) {
            $result['locations'][$location->valueObject->id] = $this->getRestFormat($location->valueObject);
        }

        foreach ($subitemsLocationIds as $id) {
            $subitems = $this->searchService->findLocations(
                new LocationQuery([
                    'filter' => new Query\Criterion\ParentLocationId($id),
                    'limit' => $limit,
                ])
            );

            $result['subitems'][$id] = [
                'totalCount' => $subitems->totalCount,
                'locations' => array_map(function (SearchHit $searchHit) {
                    return $this->getRestFormat($searchHit->valueObject);
                }, $subitems->searchHits),
            ];
        }

        return new JsonResponse($result);
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\ValueObject $valueObject
     *
     * @return array
     */
    protected function getRestFormat($valueObject): array
    {
        return json_decode(
            $this->visitor->visit($valueObject)->getContent(),
            true
        );
    }
}
