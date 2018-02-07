<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\BusinessContext;

use EzSystems\EzPlatformAdminUi\Behat\PageElement\LanguagePicker;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\LeftMenu;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\RightMenu;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\UniversalDiscoveryWidget;
use EzSystems\EzPlatformAdminUi\Behat\PageObject\ContentStructurePage;
use EzSystems\EzPlatformAdminUi\Behat\PageObject\PageObjectFactory;
use PHPUnit\Framework\Assert;

class ContentViewContext extends BusinessContext
{
    /**
     * @Given I start creating a new Landing Page :name
     */
    public function startCreatingNewLandingPage($name)
    {
        $contentStructurePage = PageObjectFactory::createPage($this->utilityContext, ContentStructurePage::PAGE_NAME);
        $contentStructurePage->startCreatingContent('Landing page');

        $this->utilityContext->getSession()->getPage()->findField('Title')->setValue($name);
        $this->utilityContext->getSession()->getPage()->findField('Description')->setValue('Test desc');
    }

    /**
     * @Given I start creating a new Article :name
     */
    public function startCreatingArticle($name)
    {
        $contentStructurePage = PageObjectFactory::createPage($this->utilityContext, ContentStructurePage::PAGE_NAME);
        $contentStructurePage->startCreatingContent('Article');

        $this->utilityContext->getSession()->getPage()->findField('Title')->setValue($name);

        $summaryField = $this->utilityContext->findElement('.ez-data-source__richtext');//$this->utilityContext->getSession()->getPage()->findField('Summary');
        $summaryField->click();
        $summaryField->setValue('Test desc');
    }

    /**
     * @Given I start editing the content in :language language
     */
    public function startEditingContent($language)
    {
        $rightMenu = new RightMenu($this->utilityContext);
        $rightMenu->clickButton('Edit');

        $languagePicker = new LanguagePicker($this->utilityContext);
        $languagePicker->chooseLanguage($language);
    }

    /**
     * @Given I open UDW and go to :itemPath
     */
    public function iOpenUDWAndGoTo($itemPath)
    {
        $leftMenu = new LeftMenu($this->utilityContext);
        $leftMenu->clickButton('Browse');

        $udw = new UniversalDiscoveryWidget($this->utilityContext);
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
        Assert::assertEquals($title, $contentStructurePage->getPageHeaderTitle());
    }
}
