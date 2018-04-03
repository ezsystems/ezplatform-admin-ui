<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\BusinessContext;

use EzSystems\EzPlatformAdminUi\Behat\PageElement\ElementFactory;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\LanguagePicker;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\LeftMenu;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\RightMenu;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\UniversalDiscoveryWidget;
use EzSystems\EzPlatformAdminUi\Behat\PageObject\ContentStructurePage;
use EzSystems\EzPlatformAdminUi\Behat\PageObject\PageObjectFactory;
use EzSystems\EzPlatformAdminUi\UI\Value\Content\Language;
use PHPUnit\Framework\Assert;

class ContentViewContext extends BusinessContext
{
    /**
     * @Given I start creating a new Landing Page :name
     */
    public function startCreatingNewLandingPage(string $name): void
    {
        $contentStructurePage = PageObjectFactory::createPage($this->utilityContext, ContentStructurePage::PAGE_NAME);
        $updatePage = $contentStructurePage->startCreatingContent('Landing page');

        $updatePage->updateForm->fillFIeldWithValue('Title', $name);
        $updatePage->updateForm->fillFIeldWithValue('Description', $name);
    }

    /**
     * @Given I start creating a new Article :name
     */
    public function startCreatingArticle(string $name): void
    {
        $contentStructurePage = PageObjectFactory::createPage($this->utilityContext, ContentStructurePage::PAGE_NAME);
        $updatePage = $contentStructurePage->startCreatingContent('Article');

        $updatePage->updateForm->fillFIeldWithValue('Title', $name);
        $updatePage->updateForm->fillRichtextWithValue('Test desc');
    }

    /**
     * @given I start editing the content
     * @Given I start editing the content in :language language
     */
    public function startEditingContent(string $language = null): void
    {
        $rightMenu = new RightMenu($this->utilityContext);
        $rightMenu->clickButton('Edit');

        $languagePicker = ElementFactory::createElement($this->utilityContext, LanguagePicker::ELEMENT_NAME);

        if ($languagePicker->isVisible()) {
            $availableLanguages = $languagePicker->getLanguages();
            Assert::assertGreaterThan(1, count($availableLanguages));
            Assert::assertContains($language, $availableLanguages);
            $languagePicker->chooseLanguage($language);
        }
    }

    /**
     * @Given I open UDW and go to :itemPath
     */
    public function iOpenUDWAndGoTo(string $itemPath): void
    {
        $leftMenu = new LeftMenu($this->utilityContext);
        $leftMenu->verifyVisibility();
        $leftMenu->clickButton('Browse');

        $udw = ElementFactory::createElement($this->utilityContext, UniversalDiscoveryWidget::ELEMENT_NAME);
        $udw->verifyVisibility();
        $udw->selectContent($itemPath);
        $udw->confirm();
    }

    /**
     * @Then I (should) see :title title/topic
     */
    public function iSeeTitle(string $title): void
    {
        $contentStructurePage = PageObjectFactory::createPage($this->utilityContext, ContentStructurePage::PAGE_NAME);
        Assert::assertEquals($title, $contentStructurePage->getPageTitle());
    }
}
