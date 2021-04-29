<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\UI\Module\ContentTree;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\Exceptions\NotImplementedException;
use eZ\Publish\API\Repository\SearchService;
use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use eZ\Publish\API\Repository\Values\Content\Query\SortClause;
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
    private const SORT_CLAUSE_MAP = [
        'DatePublished' => SortClause\DatePublished::class,
        'ContentName' => SortClause\ContentName::class,
    ];

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
        $node = $this->buildNode($location, $uninitializedContentInfoList, $loadSubtreeRequestNode, $loadChildren, $depth, $sortClause, $sortOrder);
        $contentById = $this->contentService->loadContentListByContentInfo($uninitializedContentInfoList);
        $this->supplyTranslatedContentName($node, $contentById);

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
        $searchQuery = $this->getSearchQuery($parentLocation);

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
    private function getSearchQuery(Location $parentLocation): LocationQuery
    {
        $searchQuery = new LocationQuery();
        $searchQuery->filter = new Criterion\ParentLocationId($parentLocation->id);

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
     * @param \eZ\Publish\API\Repository\Values\Content\Location $parentLocation
     *
     * @return int
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     */
    private function countSubitems(Location $parentLocation): int
    {
        $searchQuery = $this->getSearchQuery($parentLocation);

        $searchQuery->limit = 0;
        $searchQuery->offset = 0;
        $searchQuery->performCount = true;

        return $this->searchService->findLocations($searchQuery)->totalCount;
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

        $limit = $this->resolveLoadLimit($loadSubtreeRequestNode);
        $offset = null !== $loadSubtreeRequestNode
            ? $loadSubtreeRequestNode->offset
            : 0;

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
                    $childLoadSubtreeRequestNode,
                    null !== $childLoadSubtreeRequestNode,
                    $depth + 1,
                    null,
                    Query::SORT_ASC
                );
            }
        } else {
            $totalChildrenCount = 0;
            if ($contentType && $contentType->isContainer) {
                $totalChildrenCount = $this->countSubitems($location);
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
        $node->name = $this->translationHelper->getTranslatedContentName($contentById[$node->contentId]);
        foreach ($node->children as $child) {
            $this->supplyTranslatedContentName($child, $contentById);
        }
    }
}
