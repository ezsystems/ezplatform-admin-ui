<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Pagination\Pagerfanta;

use eZ\Publish\API\Repository\BookmarkService;
use EzSystems\EzPlatformAdminUi\UI\Dataset\DatasetFactory;
use Pagerfanta\Adapter\AdapterInterface;

class BookmarkAdapter implements AdapterInterface
{
    /** @var \eZ\Publish\API\Repository\BookmarkService */
    private $bookmarkService;

    /** @var \EzSystems\EzPlatformAdminUi\UI\Dataset\DatasetFactory */
    private $datasetFactory;

    /**
     * @param \eZ\Publish\API\Repository\BookmarkService $bookmarkService
     * @param \EzSystems\EzPlatformAdminUi\UI\Dataset\DatasetFactory $datasetFactory
     */
    public function __construct(BookmarkService $bookmarkService, DatasetFactory $datasetFactory)
    {
        $this->bookmarkService = $bookmarkService;
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
        return $this->bookmarkService->loadBookmarks()->totalCount;
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
            ->bookmarks()
            ->load($offset, $length)
            ->getBookmarks();
    }
}
