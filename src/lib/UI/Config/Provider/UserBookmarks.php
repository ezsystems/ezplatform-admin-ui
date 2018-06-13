<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\UI\Config\Provider;

use EzSystems\EzPlatformAdminUi\UI\Config\ProviderInterface;
use eZ\Publish\API\Repository\BookmarkService;
use EzSystems\EzPlatformAdminUi\UI\Value\ValueFactory;

/**
 * Provides information about user bookmarks.
 */
class UserBookmarks implements ProviderInterface
{
    /** @var \eZ\Publish\API\Repository\BookmarkService */
    private $bookmarkService;

    /** @var \EzSystems\EzPlatformAdminUi\UI\Value\ValueFactory */
    private $valueFactory;

    /**
     * @param \eZ\Publish\API\Repository\BookmarkService $bookmarkService
     */
    public function __construct(BookmarkService $bookmarkService, ValueFactory $valueFactory)
    {
        $this->bookmarkService = $bookmarkService;
        $this->valueFactory = $valueFactory;
    }

    /**
     * @return array
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\BadStateException
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
    public function getConfig(): array
    {
        $config = [];

        $bookmarks = $this->bookmarkService->loadBookmarks(0, 10);
        $config['count'] = $bookmarks->totalCount;

        foreach ($bookmarks->items as $location) {
            $bookmark = $this->valueFactory->createBookmark($location);
            $config['items'][] = [
                'Location' => [
                    'id' => $location->id,
                    'ContentInfo' => [
                        'Content' => [
                            'Name' => $location->contentInfo->name,
                            'ContentTypeInfo' => [
                                'identifier' => $bookmark->contentType->identifier,
                            ],
                        ],
                    ],
                ],
            ];
        }

        return $config;
    }
}
