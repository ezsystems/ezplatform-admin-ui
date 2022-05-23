<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\AdminUi\Behat\BrowserContext;

use Behat\Behat\Context\Context;
use EzSystems\Behat\Core\Behat\ArgumentParser;
use Ibexa\AdminUi\Behat\Component\UniversalDiscoveryWidget;
use PHPUnit\Framework\Assert;

class UDWContext implements Context
{
    private $argumentParser;

    /** @var \Ibexa\AdminUi\Behat\Component\UniversalDiscoveryWidget */
    private $universalDiscoveryWidget;

    public function __construct(ArgumentParser $argumentParser, UniversalDiscoveryWidget $universalDiscoveryWidget)
    {
        $this->argumentParser = $argumentParser;
        $this->universalDiscoveryWidget = $universalDiscoveryWidget;
    }

    /**
     * @When I select content :pathToContent through UDW
     */
    public function iSelectContent(string $pathToContent): void
    {
        $pathToContent = $this->argumentParser->replaceRootKeyword($pathToContent);

        $this->universalDiscoveryWidget->verifyIsLoaded();
        $this->universalDiscoveryWidget->selectContent($pathToContent);
    }

    /**
     * @When I select content root node through UDW
     */
    public function iSelectRootNodeContent(): void
    {
        $rootContentName = $this->argumentParser->replaceRootKeyword('root');
        $this->iSelectContent($rootContentName);
    }

    /** @When I close the UDW window */
    public function iCloseUDW(): void
    {
        $this->universalDiscoveryWidget->cancel();
    }

    /** @When I confirm the selection in UDW */
    public function iConfirmSelection(): void
    {
        $this->universalDiscoveryWidget->confirm();
    }

    /**
     * @Given I bookmark the Content Item :itemPath in Universal Discovery Widget
     */
    public function bookmarkContentItem(string $itemPath): void
    {
        $this->universalDiscoveryWidget->bookmarkContentItem();
    }

    /**
     * @Given it is marked as bookmarked in Universal Discovery Widget
     */
    public function itemIsMarkedAsBookmarked(): void
    {
        Assert::assertTrue($this->universalDiscoveryWidget->isBookmarked());
    }

    /**
     * @Given I change the UDW tab to :tabName
     */
    public function changeUDWTab($tabName): void
    {
        $this->universalDiscoveryWidget->changeTab($tabName);
    }

    /**
     * @Given I select bookmarked content :bookmarkName through UDW
     */
    public function iSelectBookmarkedConent(string $bookmarkName): void
    {
        $this->universalDiscoveryWidget->selectBookmark($bookmarkName);
    }

    /**
     * @Given I preview selected content
     */
    public function previewBookmarkedContent(): void
    {
        $this->universalDiscoveryWidget->openPreview();
    }

    /**
     * @Given I search for content item :item through UDW
     */
    public function searchForContentItem(string $item): void
    {
        $this->universalDiscoveryWidget->searchForContent($item);
    }

    /**
     * @Given I select :item (content) item in search results through UDW
     */
    public function selectItemInSearchResults(string $item): void
    {
        $this->universalDiscoveryWidget->selectInSearchResults($item);
    }
}
