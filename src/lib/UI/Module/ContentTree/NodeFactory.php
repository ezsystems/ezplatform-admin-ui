<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\UI\Module\ContentTree;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\Exceptions\NotImplementedException;
use eZ\Publish\API\Repository\Exceptions\OutOfBoundsException;
use eZ\Publish\API\Repository\SearchService;
use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use eZ\Publish\API\Repository\Values\Content\Query\SortClause;
use eZ\Publish\API\Repository\Values\Content\Search\AggregationResult\TermAggregationResult;
use eZ\Publish\API\Repository\Values\Content\Search\SearchResult;
use eZ\Publish\Core\Base\Exceptions\InvalidArgumentException;
use eZ\Publish\Core\Helper\TranslationHelper;
use eZ\Publish\Core\MVC\ConfigResolverInterface;
use EzSystems\EzPlatformAdminUi\REST\Value\ContentTree\LoadSubtreeRequestNode;
use EzSystems\EzPlatformAdminUi\REST\Value\ContentTree\Node;


/**
 * @internal
 */
final class NodeFactory
{
    private const TOP_NODE_CONTENT_ID = 0;
    private const SORT_CLAUSE_MAP = [
        'DatePublished' => SortClause\DatePublished::class,
        'ContentName' => SortClause\ContentName::class,
    ];
    private const MAX_AGGREGATED_LOCATION_IDS = 100;

    /** @var \eZ\Publish\API\Repository\ContentService */
    private $contentService;

    /** @var \eZ\Publish\API\Repository\SearchService */
    private $searchService;

    /** @var \eZ\Publish\Core\Helper\TranslationHelper */
    private $translationHelper;

    /** @var \eZ\Publish\Core\MVC\ConfigResolverInterface */
    private $configResolver;

    public function __construct(
        ContentService $contentService,
        SearchService $searchService,
        TranslationHelper $translationHelper,
        ConfigResolverInterface $configResolver
    ) {
        $this->contentService = $contentService;
        $this->searchService = $searchService;
        $this->translationHelper = $translationHelper;
        $this->configResolver = $configResolver;
    }

    /**
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
    public function createNode(
        Location $location,
        ?LoadSubtreeRequestNode $loadSubtreeRequestNode = null,
        bool $loadChildren = false,
        int $depth = 0,
        ?string $sortClause = null,
        string $sortOrder = Query::SORT_ASC
    ): Node {
        $uninitializedContentInfoList = [];
        $containerLocations = [];
        $node = $this->buildNode($location, $uninitializedContentInfoList, $containerLocations, $loadSubtreeRequestNode, $loadChildren, $depth, $sortClause, $sortOrder);
        $contentById = $this->contentService->loadContentListByContentInfo($uninitializedContentInfoList);

        $aggregatedChildrenCount = null;
        if ($this->searchService->supports(SearchService::CAPABILITY_AGGREGATIONS)) {
            $aggregatedChildrenCount = $this->countAggregatedSubitems($containerLocations);
        }

        $this->supplyTranslatedContentName($node, $contentById);
        $this->supplyChildrenCount($node, $aggregatedChildrenCount);

        return $node;
    }

    /**
     * @param \EzSystems\EzPlatformAdminUi\REST\Value\ContentTree\LoadSubtreeRequestNode|null $loadSubtreeRequestNode
     *
     * @return int
     */
    private function resolveLoadLimit(?LoadSubtreeRequestNode $loadSubtreeRequestNode): int
    {
        $limit = $this->getSetting('load_more_limit');

        if (null !== $loadSubtreeRequestNode) {
            $limit = $loadSubtreeRequestNode->limit;
        }

        if ($limit > $this->getSetting('children_load_max_limit')) {
            $limit = $this->getSetting('children_load_max_limit');
        }

        return $limit;
    }

    private function findSubitems(
        Location $parentLocation,
        int $limit = 10,
        int $offset = 0,
        ?string $sortClause = null,
        string $sortOrder = Query::SORT_ASC
    ): SearchResult {
        $searchQuery = $this->getSearchQuery($parentLocation->id);

        $searchQuery->limit = $limit;
        $searchQuery->offset = $offset;
        $searchQuery->sortClauses = $this->getSortClauses($sortClause, $sortOrder, $parentLocation);

        return $this->searchService->findLocations($searchQuery);
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Location $parentLocation
     *
     * @return \eZ\Publish\API\Repository\Values\Content\LocationQuery
     */
    private function getSearchQuery(int $parentLocationId): LocationQuery
    {
        $searchQuery = new LocationQuery();
        $searchQuery->filter = new Criterion\ParentLocationId($parentLocationId);

        $contentTypeCriterion = null;

        if (!empty($this->getSetting('allowed_content_types'))) {
            $contentTypeCriterion = new Criterion\ContentTypeIdentifier($this->getSetting('allowed_content_types'));
        }

        if (empty($this->allowedContentTypes) && !empty($this->getSetting('ignored_content_types'))) {
            $contentTypeCriterion = new Criterion\LogicalNot(
                new Criterion\ContentTypeIdentifier($this->getSetting('ignored_content_types'))
            );
        }

        if (null !== $contentTypeCriterion) {
            $searchQuery->filter = new Criterion\LogicalAnd([$searchQuery->filter, $contentTypeCriterion]);
        }

        return $searchQuery;
    }

    /**
     * @param int $locationId
     * @param \EzSystems\EzPlatformAdminUi\REST\Value\ContentTree\LoadSubtreeRequestNode $loadSubtreeRequestNode
     *
     * @return \EzSystems\EzPlatformAdminUi\REST\Value\ContentTree\LoadSubtreeRequestNode|null
     */
    private function findChild(int $locationId, LoadSubtreeRequestNode $loadSubtreeRequestNode): ?LoadSubtreeRequestNode
    {
        foreach ($loadSubtreeRequestNode->children as $child) {
            if ($child->locationId === $locationId) {
                return $child;
            }
        }

        return null;
    }

    /**
     * @param int $parentLocationId
     *
     * @return int
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     */
    private function countSubitems(int $parentLocationId): int
    {
        $searchQuery = $this->getSearchQuery($parentLocationId);

        $searchQuery->limit = 0;
        $searchQuery->offset = 0;
        $searchQuery->performCount = true;

        return $this->searchService->findLocations($searchQuery)->totalCount;
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Location[] $containerLocations
     *
     * @return array
     */
    private function countAggregatedSubitems(array $containerLocations): array
    {
        if (empty($containerLocations)) {
            return [];
        }

        if (count($containerLocations) > self::MAX_AGGREGATED_LOCATION_IDS) {
            $containerLocationsChunks = array_chunk($containerLocations, self::MAX_AGGREGATED_LOCATION_IDS);

            $result = [];
            foreach ($containerLocationsChunks as $containerLocationsChunk) {
                $result = array_replace($result, $this->countAggregatedSubitems($containerLocationsChunk));
            }

            return $result;
        }

        $parentLocationIds = array_column($containerLocations, 'id');

        $searchQuery = new LocationQuery();
        $searchQuery->filter = new Criterion\ParentLocationId($parentLocationIds);
        $locationChildrenTermAggregation = new Query\Aggregation\Location\LocationChildrenTermAggregation('childrens');
        $locationChildrenTermAggregation->setLimit(count($parentLocationIds));
        $searchQuery->aggregations[] = $locationChildrenTermAggregation;

        $result = $this->searchService->findLocations($searchQuery);

        try {
            return $this->aggregationResultToArray($result->aggregations->get('childrens'));
        } catch (OutOfBoundsException $e) {
        }

        return [];
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Search\AggregationResult\TermAggregationResult $aggregationResult
     *
     * @return array
     */
    private function aggregationResultToArray(TermAggregationResult $aggregationResult): array
    {
        $resultsAsArray = [];
        foreach ($aggregationResult->getEntries() as $entry) {
            /** @var \eZ\Publish\API\Repository\Values\Content\Location $location */
            $location = $entry->getKey();
            $resultsAsArray[$location->id] = $entry->getCount();
        }

        return $resultsAsArray;
    }

    private function getSetting(string $name)
    {
        return $this->configResolver->getParameter("content_tree_module.$name");
    }

    /**
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     */
    private function buildSortClause(string $sortClause, string $sortOrder): SortClause
    {
        if (!isset(static::SORT_CLAUSE_MAP[$sortClause])) {
            throw new InvalidArgumentException('$sortClause', 'Invalid sort clause');
        }

        $map = static::SORT_CLAUSE_MAP;

        /** @var \eZ\Publish\API\Repository\Values\Content\Query\SortClause $sortClauseInstance */
        $sortClauseInstance = new $map[$sortClause]();
        $sortClauseInstance->direction = $sortOrder;

        return $sortClauseInstance;
    }

    /**
     * @return \eZ\Publish\API\Repository\Values\Content\Query\SortClause[]
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     */
    private function getSortClauses(
        ?string $sortClause,
        string $sortOrder,
        Location $parentLocation
    ): array {
        if ($sortClause) {
            return [$this->buildSortClause($sortClause, $sortOrder)];
        }

        try {
            return $parentLocation->getSortClauses();
        } catch (NotImplementedException $e) {
            return []; // rely on storage engine default sorting
        }
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\ContentInfo[] $uninitializedContentInfoList
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
    private function buildNode(
        Location $location,
        array &$uninitializedContentInfoList,
        array &$containerLocations,
        ?LoadSubtreeRequestNode $loadSubtreeRequestNode = null,
        bool $loadChildren = false,
        int $depth = 0,
        ?string $sortClause = null,
        string $sortOrder = Query::SORT_ASC
    ): Node {
        $contentInfo = $location->getContentInfo();
        $contentId = $location->contentId;
        if (!isset($uninitializedContentInfoList[$contentId])) {
            $uninitializedContentInfoList[$contentId] = $contentInfo;
        }

        // Top Level Location (id = 1) does not have a Content Type
        $contentType = $location->depth > 0
            ? $contentInfo->getContentType()
            : null;

        if ($contentType && $contentType->isContainer) {
            $containerLocations[] = $location;
        }

        $limit = $this->resolveLoadLimit($loadSubtreeRequestNode);
        $offset = null !== $loadSubtreeRequestNode
            ? $loadSubtreeRequestNode->offset
            : 0;

        $totalChildrenCount = 0;
        $children = [];
        if ($loadChildren && $depth < $this->getSetting('tree_max_depth')) {
            $searchResult = $this->findSubitems($location, $limit, $offset, $sortClause, $sortOrder);
            $totalChildrenCount = $searchResult->totalCount;

            /** @var \eZ\Publish\API\Repository\Values\Content\Location $childLocation */
            foreach (array_column($searchResult->searchHits, 'valueObject') as $childLocation) {
                $childLoadSubtreeRequestNode = null !== $loadSubtreeRequestNode
                    ? $this->findChild($childLocation->id, $loadSubtreeRequestNode)
                    : null;

                $children[] = $this->buildNode(
                    $childLocation,
                    $uninitializedContentInfoList,
                    $containerLocations,
                    $childLoadSubtreeRequestNode,
                    null !== $childLoadSubtreeRequestNode,
                    $depth + 1,
                    null,
                    Query::SORT_ASC
                );
            }
        }

        return new Node(
            $depth,
            $location->id,
            $location->contentId,
            '', // node name will be provided later by `supplyTranslatedContentName` method
            $contentType ? $contentType->identifier : '',
            $contentType ? $contentType->isContainer : true,
            $location->invisible || $location->hidden,
            $limit,
            $totalChildrenCount,
            $children
        );
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Content[] $contentById
     */
    private function supplyTranslatedContentName(Node $node, array $contentById): void
    {
        if ($node->contentId !== self::TOP_NODE_CONTENT_ID) {
            $node->name = $this->translationHelper->getTranslatedContentName($contentById[$node->contentId]);
        }

        foreach ($node->children as $child) {
            $this->supplyTranslatedContentName($child, $contentById);
        }
    }

    /**
     * @param array|null $aggregationResult
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     */
    private function supplyChildrenCount(Node $node, array $aggregationResult = null): void
    {
        if ($node->isContainer) {
            if ($aggregationResult) {
                $totalCount = isset($aggregationResult[$node->locationId]) ?
                    $aggregationResult[$node->locationId] :
                    0;
            } else {
                $totalCount = $this->countSubitems($node->locationId);
            }

            $node->totalChildrenCount = $totalCount;
        }

        foreach ($node->children as $child) {
            $this->supplyChildrenCount($child, $aggregationResult);
        }
    }
}
