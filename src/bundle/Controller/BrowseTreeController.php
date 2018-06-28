<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUiBundle\Controller;

use EzSystems\EzPlatformAdminUi\UI\Module\BrowseTree\Node;
use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\Exceptions\NotImplementedException;
use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\SearchService;
use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use eZ\Publish\Core\Base\Exceptions\InvalidArgumentException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;

class BrowseTreeController extends Controller
{
    /** @var LocationService */
    protected $locationService;

    /** @var ContentTypeService */
    protected $contentTypeService;

    /** @var SearchService */
    protected $searchService;

    /** @var RouterInterface */
    private $router;

    /** @var int */
    protected $paginationChildren;

    /** @var array|null */
    protected $excludeContentTypes;

    /**
     * BrowseTreeController constructor.
     *
     * @param LocationService    $locationService
     * @param ContentTypeService $contentTypeService
     * @param SearchService      $searchService
     * @param RouterInterface    $router
     * @param int                $paginationChildren
     * @param array|null         $excludeContentTypes
     */
    public function __construct(
        LocationService $locationService,
        ContentTypeService $contentTypeService,
        SearchService $searchService,
        RouterInterface $router,
        int $paginationChildren,
        ?array $excludeContentTypes
    ) {
        $this->locationService = $locationService;
        $this->contentTypeService = $contentTypeService;
        $this->searchService = $searchService;
        $this->router = $router;
        $this->paginationChildren = $paginationChildren;
        $this->excludeContentTypes = $excludeContentTypes;
    }

    /**
     * @param Location $location
     *
     * @return Response
     *
     * @throws InvalidArgumentException
     * @throws NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
    public function initAction(Location $location): Response
    {
        $response = new JsonResponse();

        foreach ((array)$location->pathString as $pathString) {
            if (preg_match('/^(\/\w+)+\/$/', $pathString) !== 1) {
                throw new InvalidArgumentException(
                    "value '$location->pathString' must follow the pathString format, eg /1/2/"
                );
            }
        }

        $parentData = null;
        $pathString = array_reverse(explode('/', trim($location->pathString, '/')));
        foreach ($pathString as $key => $locationId) {
            if ($key == count($pathString) - 1) {
                continue;
            }

            $parentLocation = $this->locationService->loadLocation($locationId);
            $nodeData = new Node($parentLocation->id);
            $nodeData->text = $parentLocation->contentInfo->name;
            $nodeData->type = $this->contentTypeService->loadContentType($parentLocation->contentInfo->contentTypeId)->identifier;
            $nodeData->icon = 'ct-icon ct-' . $nodeData->type;

            $nodeData->li_attr = [
                'location-visible' => !$parentLocation->invisible,
                'location-hidden' => $parentLocation->hidden,
                'location-selected' => $key == 0 ? 'true' : 'false',
            ];
            $nodeData->a_attr = [
                'href' => $this->router->generate('_ezpublishLocation', ['locationId' => $parentLocation->id]),
                'children' => $this->router->generate('ezplatform.browsetree.children', ['locationId' => $parentLocation->id, 'offset' => 0]),
                'title' => $nodeData->text,
            ];
            $nodeData->state = ['opened' => true];

            if (!$parentData) {
                $nodeData->children = $this->findNodes($parentLocation, null, 0, true);
            } else {
                $nodeData->children = $this->findNodes($parentLocation, $parentData, 0, true);
            }

            $parentData = $nodeData;
        }

        $response->setData(
            [$parentData]
        );

        return $response;
    }

    /**
     * @param Location $location
     * @param int      $offset
     *
     * @return Response
     */
    public function childrenAction(Location $location, int $offset = 0): Response
    {
        $response = new JsonResponse();

        try {
            $children = $this->findNodes($location, null, $offset);
        } catch (NotFoundException $e) {
            $children = null;
        }

        $response->setData([
            'children' => $children,
            'next' => count($children) > 0 ? $this->router->generate('ezplatform.browsetree.children', [
                'locationId' => $location->id,
                'offset' => $offset + $this->paginationChildren,
            ]) : false,
        ]);

        return $response;
    }

    /**
     * @param Location  $location
     * @param Node|null $node
     * @param int       $offset
     * @param bool      $init
     *
     * @return array|null
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     */
    protected function findNodes(Location $location, ?Node $node = null, int $offset = 0, bool $init = false): ?array
    {
        try {
            $limit = $init ? $this->loadLocationChildrenCount($location) : $this->paginationChildren;
            $children = $this->loadLocationChildren($location, $offset, $limit);
        } catch (NotImplementedException $e) {
            $children = [
                'childLocations' => [],
                'childLocationsCount' => 0,
            ];
        }

        $nodes = [];
        foreach ($children['childLocations'] as $child) {
            if ($node && $child->id == $node->id) {
                $nodes[] = $node;
                continue;
            }

            $childNode = new Node($child->id);
            $childNode->text = $child->contentInfo->name;
            $childNode->type = $this->contentTypeService->loadContentType($child->contentInfo->contentTypeId)->identifier;
            $childNode->icon = 'ct-icon ct-' . $childNode->type;
            $childNode->li_attr = [
                'location-visible' => !$child->invisible,
                'location-hidden' => $child->hidden,
            ];
            $childNode->a_attr = [
                'href' => $this->router->generate('_ezpublishLocation', ['locationId' => $child->id]),
                'children' => $this->router->generate('ezplatform.browsetree.children', ['locationId' => $child->id, 'offset' => $offset]),
                'title' => $childNode->text,
            ];

            if ($this->loadLocationChildrenCount($child)) {
                $nodesTemp = new Node();
                $nodesTemp->text = '...';
                $childNode->children = [$nodesTemp];
            }

            $nodes[] = $childNode;
        }

        if (empty($nodes)) {
            return null;
        }

        return $nodes;
    }

    /**
     * @param Location $location
     * @param int      $offset
     * @param int      $limit
     *
     * @return array
     *
     * @throws NotImplementedException
     */
    protected function loadLocationChildren(Location $location, $offset = 0, $limit = 25): array
    {
        $filters = [new Criterion\ParentLocationId($location->id)];

        if ($this->excludeContentTypes) {
            $excludeContentTypesFilter = [];
            foreach ($this->excludeContentTypes as $contentType) {
                $excludeContentTypesFilter[] = new Criterion\LogicalNot(new Criterion\ContentTypeIdentifier($contentType));
            }

            $filters[] = new Criterion\LogicalAnd($excludeContentTypesFilter);
        }

        $query = new LocationQuery([
            'filter' => new Criterion\LogicalAnd($filters),
            'offset' => $offset,
            'limit' => $limit,
            'sortClauses' => $location->getSortClauses(),
        ]);

        $childLocations = [];
        try {
            $searchResult = $this->searchService->findLocations($query);
        } catch (\eZ\Publish\API\Repository\Exceptions\InvalidArgumentException $e) {
            return $childLocations;
        }

        foreach ($searchResult->searchHits as $searchHit) {
            $childLocations[] = $searchHit->valueObject;
        }

        return [
            'childLocations' => $childLocations,
            'childLocationsCount' => $searchResult->totalCount,
        ];
    }

    /**
     * @param Location $location
     *
     * @return int
     *
     * @throws NotImplementedException
     */
    protected function loadLocationChildrenCount(Location $location): int
    {
        $children = $this->loadLocationChildren($location, 0, 1);

        return $children['childLocationsCount'];
    }
}
