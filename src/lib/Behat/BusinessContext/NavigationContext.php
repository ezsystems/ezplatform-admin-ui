<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\BusinessContext;

use EzSystems\EzPlatformAdminUi\Behat\PageElement\Breadcrumb;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\ElementFactory;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\UpperMenu;
use EzSystems\EzPlatformAdminUi\Behat\PageObject\PageObjectFactory;

/** Context for general navigation actions */
class NavigationContext extends BusinessContext
{
    /**
     * @Given I open :pageName page
     */
    public function openPage(string $pageName): void
    {
        $page = PageObjectFactory::createPage($this->utilityContext, $pageName);
        $page->open();
    }

    /**
     * @Given I try to open :pageName page
     */
    public function tryToOpenPage(string $pageName): void
    {
        $page = PageObjectFactory::createPage($this->utilityContext, $pageName);
        $page->open(false);
    }

    /**
     * @Then I should be on :pageName page
     * @Then I should be on :pageName :itemName page
     */
    public function iAmOnPage(string $pageName, string $itemName = null): void
    {
        $page = PageObjectFactory::createPage($this->utilityContext, $pageName, $itemName);
        $page->verifyIsLoaded();
    }

    /**
     * @Then I go to :tab tab
     * @Then I go to :subTab in :tab tab
     */
    public function iGoToTab(string $tabName, string $subTab = null): void
    {
        $upperMenu = ElementFactory::createElement($this->utilityContext, UpperMenu::ELEMENT_NAME);
        $upperMenu->goToTab($tabName);

        if ($subTab !== null) {
            $upperMenu->goToSubTab($subTab);
        }
    }

    /**
     * @When I click on :element on breadcrumb
     */
    public function iClickOnBreadcrumbLink(string $element): void
    {
        $breadcrumb = ElementFactory::createElement($this->utilityContext, Breadcrumb::ELEMENT_NAME);
        $breadcrumb->verifyVisibility();
        $breadcrumb->clickBreadcrumbItem($element);
    }
}
