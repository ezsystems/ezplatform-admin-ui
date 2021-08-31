<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\UI\Dataset;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\Content\Relation;
use EzSystems\EzPlatformAdminUi\UI\Value\ValueFactory;

final class RelationListDataset
{
    /** @var \eZ\Publish\API\Repository\ContentService */
    private $contentService;

    /** @var \EzSystems\EzPlatformAdminUi\UI\Value\ValueFactory */
    private $valueFactory;

    /** @var \EzSystems\EzPlatformAdminUi\UI\Value\Content\RelationInterface[] */
    private $relations;

    /**
     * @param \eZ\Publish\API\Repository\ContentService $contentService
     * @param \EzSystems\EzPlatformAdminUi\UI\Value\ValueFactory $valueFactory
     */
    public function __construct(ContentService $contentService, ValueFactory $valueFactory)
    {
        $this->contentService = $contentService;
        $this->valueFactory = $valueFactory;
        $this->relations = [];
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Content $content
     *
     * @return \EzSystems\EzPlatformAdminUi\UI\Dataset\RelationListDataset
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
    public function load(
        Content $content
    ): self {
        $versionInfo = $content->getVersionInfo();

        $this->relations = array_map(
            function (Relation $relation) use ($content) {
                return $this->valueFactory->createRelation($relation, $content);
            },
            $this->contentService->loadRelations($versionInfo)
        );

        return $this;
    }

    /**
     * @return \EzSystems\EzPlatformAdminUi\UI\Value\Content\RelationInterface[]
     */
    public function getRelations(): array
    {
        return $this->relations;
    }
}
