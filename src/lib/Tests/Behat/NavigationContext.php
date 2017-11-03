<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Tests\Behat;

use EzSystems\EzPlatformAdminUi\Tests\Behat\PageObject\PageObjectFactory;

class NavigationContext extends BusinessContext
{
    /**
     * @Given I open :pageName page
     */
    public function openPage($pageName)
    {
        $page = PageObjectFactory::createPage($this->utilityContext, $pageName);
        $page->open();
    }

    /**
     * @Given I try to open :pageName page
     */
    public function tryToOpenPage($pageName)
    {
        $page = PageObjectFactory::createPage($this->utilityContext, $pageName);
        $page->open(false);
    }

    /**
     * @Then I should be on :pageName page
     */
    public function iAmOnPage($pageName)
    {
        $page = PageObjectFactory::createPage($this->utilityContext, $pageName);
        $page->verifyIsLoaded();
    }
}
