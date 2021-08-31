<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\UI\Dataset;

use eZ\Publish\API\Repository\BookmarkService;
use eZ\Publish\API\Repository\Values\Content\Location;
use EzSystems\EzPlatformAdminUi\UI\Value\ValueFactory;

class BookmarksDataset
{
    /** @var \eZ\Publish\API\Repository\BookmarkService */
    private $bookmarkService;

    /** @var \EzSystems\EzPlatformAdminUi\UI\Value\ValueFactory */
    private $valueFactory;

    /** @var \EzSystems\EzPlatformAdminUi\UI\Value\Location\Bookmark[] */
    private $data;

    /**
     * @param \eZ\Publish\API\Repository\BookmarkService $bookmarkService
     * @param \EzSystems\EzPlatformAdminUi\UI\Value\ValueFactory $valueFactory
     */
    public function __construct(
        BookmarkService $bookmarkService,
        ValueFactory $valueFactory
    ) {
        $this->bookmarkService = $bookmarkService;
        $this->valueFactory = $valueFactory;
    }

    /**
     * @param int $offset
     * @param int $limit
     *
     * @return \EzSystems\EzPlatformAdminUi\UI\Dataset\BookmarksDataset
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     */
    public function load(int $offset = 0, int $limit = 25): self
    {
        $this->data = array_map(
            function (Location $location) {
                return $this->valueFactory->createBookmark($location);
            },
            $this->bookmarkService->loadBookmarks($offset, $limit)->items
        );

        return $this;
    }

    /**
     * @return \EzSystems\EzPlatformAdminUi\UI\Value\Location\Bookmark[]
     */
    public function getBookmarks(): array
    {
        return $this->data;
    }
}
