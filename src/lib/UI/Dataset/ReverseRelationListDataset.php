<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\UI\Dataset;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\Content\RelationList\RelationListItemInterface;
use EzSystems\EzPlatformAdminUi\UI\Value\ValueFactory;

final class ReverseRelationListDataset
{
    /** @var \eZ\Publish\API\Repository\ContentService */
    private $contentService;

    /** @var \EzSystems\EzPlatformAdminUi\UI\Value\ValueFactory */
    private $valueFactory;

    /** @var \EzSystems\EzPlatformAdminUi\UI\Value\Content\RelationInterface[] */
    private $reverseRelations;

    /**
     * @param \eZ\Publish\API\Repository\ContentService $contentService
     * @param \EzSystems\EzPlatformAdminUi\UI\Value\ValueFactory $valueFactory
     */
    public function __construct(ContentService $contentService, ValueFactory $valueFactory)
    {
        $this->contentService = $contentService;
        $this->valueFactory = $valueFactory;
        $this->reverseRelations = [];
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Content $content
     * @param int $offset
     * @param int $limit
     *
     * @return \EzSystems\EzPlatformAdminUi\UI\Dataset\ReverseRelationListDataset
     */
    public function load(
        Content $content,
        int $offset = 0,
        int $limit = 10
    ): self {
        $versionInfo = $content->getVersionInfo();

        $reverseRelationListItems = $this->contentService->loadReverseRelationList(
            $versionInfo->getContentInfo(),
            $offset,
            $limit
        )->items;

        $this->reverseRelations = array_map(
            function (RelationListItemInterface $relationListItem) use ($content) {
                if ($relationListItem->hasRelation()) {
                    /** @var \eZ\Publish\API\Repository\Values\Content\RelationList\Item\RelationListItem $relationListItem */
                    return $this->valueFactory->createRelationItem(
                        $relationListItem,
                        $content
                    );
                }

                /** @var \eZ\Publish\API\Repository\Values\Content\RelationList\Item\UnauthorizedRelationListItem $relationListItem */
                return $this->valueFactory->createUnauthorizedRelationItem(
                    $relationListItem
                );
            },
            $reverseRelationListItems
        );

        return $this;
    }

    /**
     * @return \EzSystems\EzPlatformAdminUi\UI\Value\Content\RelationInterface[]
     */
    public function getReverseRelations(): array
    {
        return $this->reverseRelations;
    }
}
