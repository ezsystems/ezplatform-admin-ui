<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Data\Bookmark;

use Symfony\Component\Validator\Constraints as Assert;

class BookmarkRemoveData
{
    /**
     * @Assert\NotBlank()
     *
     * @var array
     */
    public $bookmarks;

    /**
     * @param array $bookmarks
     */
    public function __construct(array $bookmarks = [])
    {
        $this->bookmarks = $bookmarks;
    }

    /**
     * @return array
     */
    public function getBookmarks(): array
    {
        return $this->bookmarks;
    }

    /**
     * @param array $bookmarks
     */
    public function setBookmarks(array $bookmarks): void
    {
        $this->bookmarks = $bookmarks;
    }
}
