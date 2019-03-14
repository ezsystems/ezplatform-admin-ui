<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\BusinessContext;

use EzSystems\EzPlatformAdminUi\Behat\Helper\EzEnvironmentConstants;
use EzSystems\EzPlatformPageBuilder\Tests\Behat\PageObject\PageBuilderEditor;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Breadcrumb;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\ElementFactory;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\UpperMenu;
use EzSystems\EzPlatformAdminUi\Behat\PageObject\ContentItemPage;
use EzSystems\EzPlatformAdminUi\Behat\PageObject\PageObjectFactory;
use PHPUnit\Framework\Assert;

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
     * @Given I go to dashboard
     */
    public function iGoToDashboard(): void
    {
        $upperMenu = ElementFactory::createElement($this->utilityContext, UpperMenu::ELEMENT_NAME);
        $upperMenu->goToDashboard();
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

    /**
     * @Given I navigate to content :contentName of type :contentType in :path
     * @Given I navigate to content :contentName of type :contentType
     */
    public function iNavigateToContent(string $contentName, string $contentType, string $path = null)
    {
        $contentPage = PageObjectFactory::createPage($this->utilityContext, ContentItemPage::PAGE_NAME, $contentName);
        if ($path !== null) {
            $contentPage->navigateToPath($path);
        }
        $contentPage->goToSubItem($contentName, $contentType);
    }

    /**
     * @Given I navigate to content :contentName of type :contentType in root path
     */
    public function iNavigateToContentInRoot(string $contentName, string $contentType)
    {
        $path = EzEnvironmentConstants::get('ROOT_CONTENT_NAME');
        $this->iNavigateToContent($contentName, $contentType, $path);
    }

    /**
     * @Then breadcrumb shows :path path
     */
    public function verifyIfBreadcrumbShowsPath(string $path): void
    {
        $breadcrumb = ElementFactory::createElement($this->utilityContext, Breadcrumb::ELEMENT_NAME);
        Assert::assertEquals(
            str_replace('/', ' ', $path),
            $breadcrumb->getBreadcrumb(),
            'Breadcrumb shows invalid path'
        );
    }

    /**
     * @Then breadcrumb shows :path path under root path
     */
    public function verifyIfBreadcrumbShowsPathUnderRoot(string $path): void
    {
        $path = sprintf('%s/%s', EzEnvironmentConstants::get('ROOT_CONTENT_NAME'), $path);
        $this->verifyIfBreadcrumbShowsPath($path);
    }

    /**
     * @Then I should be redirected to root in default view
     */
    public function iShouldBeRedirectedToRootInDefaultView(): void
    {
        if (EzEnvironmentConstants::get('ROOT_CONTENT_TYPE') === 'Landing page') {
            $previewType = PageObjectFactory::getPreviewType(EzEnvironmentConstants::get('ROOT_CONTENT_TYPE'));
            $pageEditor = PageObjectFactory::createPage($this->utilityContext, PageBuilderEditor::PAGE_NAME, $previewType);
            $pageEditor->pagePreview->setTitle(EzEnvironmentConstants::get('ROOT_CONTENT_NAME'));
            $pageEditor->waitUntilLoaded();
            $pageEditor->verifyIsLoaded();
        } else {
            $contentItemPage = PageObjectFactory::createPage($this->utilityContext, ContentItemPage::PAGE_NAME, EzEnvironmentConstants::get('ROOT_CONTENT_NAME'));
            $contentItemPage->verifyIsLoaded();
        }
    }
}
