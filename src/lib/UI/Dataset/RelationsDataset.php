<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\UI\Dataset;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\Exceptions\UnauthorizedException;
use eZ\Publish\API\Repository\Values\Content\VersionInfo;
use EzSystems\EzPlatformAdminUi\UI\Value\ValueFactory;
use EzSystems\EzPlatformAdminUi\UI\Value as UIValue;

class RelationsDataset
{
    /** @var ContentService */
    protected $contentService;

    /** @var ValueFactory */
    protected $valueFactory;

    /** @var UIValue\Content\Relation[] */
    protected $relations;

    /** @var UIValue\Content\Relation[] */
    protected $reverseRelations;

    /**
     * @param ContentService $contentService
     * @param ValueFactory $valueFactory
     */
    public function __construct(ContentService $contentService, ValueFactory $valueFactory)
    {
        $this->contentService = $contentService;
        $this->valueFactory = $valueFactory;
        $this->relations = [];
        $this->reverseRelations = [];
    }

    /**
     * @param VersionInfo $versionInfo
     *
     * @return RelationsDataset
     *
     * @throws UnauthorizedException
     */
    public function load(VersionInfo $versionInfo): self
    {
        $contentInfo = $versionInfo->getContentInfo();

        foreach ($this->contentService->loadRelations($versionInfo) as $relation) {
            $this->relations[] = $this->valueFactory->createRelation($relation, $contentInfo);
        }

        foreach ($this->contentService->loadReverseRelations($versionInfo->getContentInfo()) as $reverseRelation) {
            $this->reverseRelations[] = $this->valueFactory->createRelation($reverseRelation, $contentInfo);
        }

        return $this;
    }

    /**
     * @return UIValue\Content\Relation[]
     */
    public function getRelations(): array
    {
        return $this->relations;
    }

    /**
     * @return UIValue\Content\Relation[]
     */
    public function getReverseRelations(): array
    {
        return $this->reverseRelations;
    }
}
