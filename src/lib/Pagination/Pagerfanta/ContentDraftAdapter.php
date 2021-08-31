<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Pagination\Pagerfanta;

use eZ\Publish\API\Repository\ContentService;
use EzSystems\EzPlatformAdminUi\UI\Dataset\DatasetFactory;
use Pagerfanta\Adapter\AdapterInterface;

final class ContentDraftAdapter implements AdapterInterface
{
    /** @var \eZ\Publish\API\Repository\ContentService */
    private $contentService;

    /** @var \EzSystems\EzPlatformAdminUi\UI\Dataset\DatasetFactory */
    private $datasetFactory;

    /**
     * @param \eZ\Publish\API\Repository\ContentService $contentService
     * @param \EzSystems\EzPlatformAdminUi\UI\Dataset\DatasetFactory $datasetFactory
     */
    public function __construct(ContentService $contentService, DatasetFactory $datasetFactory)
    {
        $this->contentService = $contentService;
        $this->datasetFactory = $datasetFactory;
    }

    /**
     * Returns the number of results.
     *
     * @return int the number of results
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     */
    public function getNbResults()
    {
        return $this->contentService->countContentDrafts();
    }

    /**
     * Returns an slice of the results.
     *
     * @param int $offset the offset
     * @param int $length the length
     *
     * @return array|\Traversable the slice
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     */
    public function getSlice($offset, $length)
    {
        return $this->datasetFactory
            ->contentDraftList()
            ->load(null, $offset, $length)
            ->getContentDrafts();
    }
}
