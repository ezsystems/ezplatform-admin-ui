<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat;

use EzSystems\EzPlatformAdminUi\Behat\PageObject\PageObjectFactory;

class NavigationContext extends BusinessContext
{
    /**
     * @Given I open :pageName page
     */
    public function openPage($pageName): void
    {
        $page = PageObjectFactory::createPage($this->utilityContext, $pageName);
        $page->open();
    }

    /**
     * @Given I try to open :pageName page
     */
    public function tryToOpenPage($pageName): void
    {
        $page = PageObjectFactory::createPage($this->utilityContext, $pageName);
        $page->open(false);
    }

    /**
     * @Then I should be on :pageName page
     */
    public function iAmOnPage($pageName): void
    {
        $page = PageObjectFactory::createPage($this->utilityContext, $pageName);
        $page->verifyIsLoaded();
    }
}
