<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Pagination\Pagerfanta;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\Values\Content\Content;
use EzSystems\EzPlatformAdminUi\UI\Dataset\DatasetFactory;
use Pagerfanta\Adapter\AdapterInterface;

final class ReverseRelationAdapter implements AdapterInterface
{
    /** @var \eZ\Publish\API\Repository\ContentService */
    private $contentService;

    /** @var \EzSystems\EzPlatformAdminUi\UI\Dataset\DatasetFactory */
    private $datasetFactory;

    /** @var \eZ\Publish\API\Repository\Values\Content\Content */
    private $content;

    /**
     * @param \eZ\Publish\API\Repository\ContentService $contentService
     * @param \EzSystems\EzPlatformAdminUi\UI\Dataset\DatasetFactory $datasetFactory
     * @param \eZ\Publish\API\Repository\Values\Content\Content $content
     */
    public function __construct(
        ContentService $contentService,
        DatasetFactory $datasetFactory,
        Content $content
    ) {
        $this->contentService = $contentService;
        $this->datasetFactory = $datasetFactory;
        $this->content = $content;
    }

    /**
     * Returns the number of results.
     *
     * @return int the number of results
     */
    public function getNbResults()
    {
        return $this->contentService->countReverseRelations($this->content->contentInfo);
    }

    /**
     * Returns an slice of the results.
     *
     * @param int $offset the offset
     * @param int $length the length
     *
     * @return array|\Traversable the slice
     */
    public function getSlice($offset, $length)
    {
        return $this->datasetFactory
            ->reverseRelationList()
            ->load($this->content, $offset, $length)
            ->getReverseRelations();
    }
}
