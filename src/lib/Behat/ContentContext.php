<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat;

use Behat\Behat\Tester\Exception\PendingException;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\LanguagePicker;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\LeftMenu;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\RightMenu;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\UniversalDiscoveryWidget;
use EzSystems\EzPlatformAdminUi\Behat\PageObject\ContentStructurePage;
use EzSystems\EzPlatformAdminUi\Behat\PageObject\PageObjectFactory;
use PHPUnit\Framework\Assert;

class ContentContext extends BusinessContext
{
    /**
     * @Given I click (on) the edit action bar button :button
     * Click on a AdminUI edit action bar
     *
     * @param  string   $button     Text of the element to click
     */
    public function clickEditActionBar($button)
    {
        $rightMenu = new RightMenu($this->utilityContext);
        $rightMenu->clickButton($button);
    }

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
     * @Then I (should) see :title title/topic
     */
    public function iSeeTitle($title)
    {
        $contentStructurePage = PageObjectFactory::createPage($this->utilityContext, ContentStructurePage::PAGE_NAME);
        Assert::assertEquals($title, $contentStructurePage->getPageHeaderTitle());
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
     * @When I set :field to :value
     * @When I set :field as empty
     */
    public function fillFieldWithValue($field, $value = '')
    {
        $fieldNode = $this->utilityContext->waitUntil(10,
            function () use ($field) {
                return $this->utilityContext->getSession()->getPage()->findField($field);
            });

        $fieldNode->setValue('');
        $fieldNode->setValue($value);
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


}
