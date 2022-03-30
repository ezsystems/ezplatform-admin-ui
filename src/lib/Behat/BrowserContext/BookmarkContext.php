<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\AdminUi\Behat\BrowserContext;

use Behat\Behat\Context\Context;
use Ibexa\AdminUi\Behat\Page\BookmarksPage;
use Ibexa\AdminUi\Behat\Page\ContentViewPage;
use PHPUnit\Framework\Assert;

class BookmarkContext implements Context
{
    /** @var \Ibexa\AdminUi\Behat\Page\ContentViewPage */
    private $contentViewPage;

    /** @var \Ibexa\AdminUi\Behat\Page\BookmarksPage */
    private $bookmarksPage;

    public function __construct(ContentViewPage $contentViewPage, BookmarksPage $bookmarksPage)
    {
        $this->contentViewPage = $contentViewPage;
        $this->bookmarksPage = $bookmarksPage;
    }

    /**
     * @Given I bookmark the Content Item :path
     */
    public function bookmarkContentItem(string $path): void
    {
        $this->contentViewPage->setExpectedLocationPath($path);
        $this->contentViewPage->verifyIsLoaded();
        $this->contentViewPage->bookmarkContentItem();
    }

    /**
     * @Given it is marked as bookmarked
     */
    public function contentItemIsBookmarked(): void
    {
        Assert::assertTrue($this->contentViewPage->isBookmarked());
    }

    /**
     * @Given there's a :contentName Content Item on Bookmarks list
     */
    public function contentItemIsDisplayed(string $contentName): void
    {
        Assert::assertTrue($this->bookmarksPage->isBookmarked($contentName));
    }

    /**
     * @Given there's no :contentName Content Item on Bookmarks list
     */
    public function contentItemIsNotDisplayed(string $contentName): void
    {
        Assert::assertFalse($this->bookmarksPage->isBookmarked($contentName));
    }

    /**
     * @Given I go to :contentName Content Item from Bookmarks
     */
    public function goToContentItem(string $contentName): void
    {
        $this->bookmarksPage->goToItem($contentName);
    }

    /**
     * @Given I start editing :contentName Content Item from Bookmarks
     */
    public function startEditingContentItem(string $contentName): void
    {
        $this->bookmarksPage->edit($contentName);
    }

    /**
     * @Given I delete the bookmark for :contentName Content Item
     */
    public function deleteFromBookmarks(string $contentName): void
    {
        $this->bookmarksPage->delete($contentName);
    }
}
