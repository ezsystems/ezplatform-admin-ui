<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\BusinessContext;

use EzSystems\Behat\Browser\Factory\PageObjectFactory;
use EzSystems\EzPlatformAdminUi\Behat\PageObject\SearchPage;

class SearchContext extends BusinessContext
{
    /**
     * @When I search for a Content named :contentItemName
     */
    public function iSearchForContent(string $contentItemName): void
    {
        $searchPage = PageObjectFactory::createPage($this->browserContext, SearchPage::PAGE_NAME);
        $searchPage->verifyIsLoaded();
        $searchPage->search($contentItemName);
    }

    /**
     * @Then I should see in search results an item named :contentItemName
     */
    public function searchResults(string $contentItemName): void
    {
        $searchPage = PageObjectFactory::createPage($this->browserContext, SearchPage::PAGE_NAME);
        $searchPage->verifyItemInSearchResults($contentItemName);
    }
}
