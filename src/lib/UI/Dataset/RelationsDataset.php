<?php

declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\UI\Dataset;

use eZ\Publish\API\Repository\ContentService;
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
    }

    /**
     * @param VersionInfo $versionInfo
     *
     * @return RelationsDataset
     */
    public function load(VersionInfo $versionInfo): self
    {
        $this->relations = array_map(
            [$this->valueFactory, 'createRelation'],
            $this->contentService->loadRelations($versionInfo)
        );
        $this->reverseRelations = array_map(
            [$this->valueFactory, 'createRelation'],
            $this->contentService->loadReverseRelations($versionInfo->getContentInfo())
        );

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
