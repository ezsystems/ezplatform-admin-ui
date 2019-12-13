<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\UI\Module\ContentTree;

use eZ\Publish\API\Repository\Exceptions\NotImplementedException;
use eZ\Publish\API\Repository\SearchService;
use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use eZ\Publish\API\Repository\Values\Content\Search\SearchResult;
use eZ\Publish\Core\Helper\TranslationHelper;
use EzSystems\EzPlatformAdminUi\REST\Value\ContentTree\LoadSubtreeRequestNode;
use EzSystems\EzPlatformAdminUi\REST\Value\ContentTree\Node;

/**
 * @internal
 */
final class NodeFactory
{
    /** @var \eZ\Publish\API\Repository\SearchService */
    private $searchService;

    /** @var \eZ\Publish\Core\Helper\TranslationHelper */
    private $translationHelper;

    /** @var int */
    private $displayLimit;

    /** @var int */
    private $childrenLoadMaxLimit;

    /** @var int */
    private $maxDepth;

    /** @var string[] */
    private $allowedContentTypes;

    /** @var string[] */
    private $ignoredContentTypes;

    /**
     * @param \eZ\Publish\API\Repository\SearchService $searchService
     * @param \eZ\Publish\Core\Helper\TranslationHelper $translationHelper
     * @param int $displayLimit
     * @param int $childrenLoadMaxLimit
     * @param int $maxDepth
     * @param array $allowedContentTypes
     * @param array $ignoredContentTypes
     */
    public function __construct(
        SearchService $searchService,
        TranslationHelper $translationHelper,
        int $displayLimit = 20,
        int $childrenLoadMaxLimit = 100,
        int $maxDepth = 10,
        array $allowedContentTypes = [],
        array $ignoredContentTypes = []
    ) {
        $this->searchService = $searchService;
        $this->translationHelper = $translationHelper;
        $this->displayLimit = $displayLimit;
        $this->childrenLoadMaxLimit = $childrenLoadMaxLimit;
        $this->maxDepth = $maxDepth;
        $this->allowedContentTypes = $allowedContentTypes;
        $this->ignoredContentTypes = $ignoredContentTypes;
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Location $location
     * @param \EzSystems\EzPlatformAdminUi\REST\Value\ContentTree\LoadSubtreeRequestNode $loadSubtreeRequestNode
     * @param bool $loadChildren
     * @param int $depth
     *
     * @return \EzSystems\EzPlatformAdminUi\REST\Value\ContentTree\Node
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
    public function createNode(
        Location $location,
        ?LoadSubtreeRequestNode $loadSubtreeRequestNode = null,
        bool $loadChildren = false,
        int $depth = 0
    ): Node {
        $content = $location->getContent();

        // Top Level Location (id = 1) does not have a Content Type
        $contentType = $location->depth > 0
            ? $content->getContentType()
            : null;

        $limit = $this->resolveLoadLimit($loadSubtreeRequestNode);
        $offset = null !== $loadSubtreeRequestNode
            ? $loadSubtreeRequestNode->offset
            : 0;

        $children = [];
        if ($depth < $this->maxDepth && $loadChildren) {
            $searchResult = $this->findSubitems($location, $limit, $offset);
            $totalChildrenCount = $searchResult->totalCount;

            /** @var \eZ\Publish\API\Repository\Values\Content\Location $childLocation */
            foreach (array_column($searchResult->searchHits, 'valueObject') as $childLocation) {
                $childLoadSubtreeRequestNode = null !== $loadSubtreeRequestNode
                    ? $this->findChild($childLocation->id, $loadSubtreeRequestNode)
                    : null;

                $children[] = $this->createNode(
                    $childLocation,
                    $childLoadSubtreeRequestNode,
                    null !== $childLoadSubtreeRequestNode,
                    $depth + 1
                );
            }
        } else {
            $totalChildrenCount = $this->countSubitems($location);
        }

        return new Node(
            $depth,
            $location->id,
            $location->contentId,
            $this->translationHelper->getTranslatedContentName($content),
            $contentType ? $contentType->identifier : '',
            $contentType ? $contentType->isContainer : true,
            $location->invisible || $location->hidden,
            $limit,
            $totalChildrenCount,
            $children
        );
    }

    /**
     * @param \EzSystems\EzPlatformAdminUi\REST\Value\ContentTree\LoadSubtreeRequestNode|null $loadSubtreeRequestNode
     *
     * @return int
     */
    private function resolveLoadLimit(?LoadSubtreeRequestNode $loadSubtreeRequestNode): int
    {
        $limit = $this->displayLimit;

        if (null !== $loadSubtreeRequestNode) {
            $limit = $loadSubtreeRequestNode->limit;
        }

        if ($limit > $this->childrenLoadMaxLimit) {
            $limit = $this->childrenLoadMaxLimit;
        }

        return $limit;
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Location $parentLocation
     * @param int $limit
     * @param int $offset
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Search\SearchResult
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     */
    private function findSubitems(Location $parentLocation, int $limit = 10, int $offset = 0): SearchResult
    {
        $searchQuery = $this->getSearchQuery($parentLocation);

        $searchQuery->limit = $limit;
        $searchQuery->offset = $offset;

        try {
            $searchQuery->sortClauses = $parentLocation->getSortClauses();
        } catch (NotImplementedException $e) {
            $searchQuery->sortClauses = []; // rely on storage engine default sorting
        }

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

        if (!empty($this->allowedContentTypes)) {
            $contentTypeCriterion = new Criterion\ContentTypeIdentifier($this->allowedContentTypes);
        }

        if (empty($this->allowedContentTypes) && !empty($this->ignoredContentTypes)) {
            $contentTypeCriterion = new Criterion\LogicalNot(
                new Criterion\ContentTypeIdentifier($this->ignoredContentTypes)
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
}
