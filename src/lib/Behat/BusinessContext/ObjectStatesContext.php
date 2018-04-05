<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\BusinessContext;

use Behat\Gherkin\Node\TableNode;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Dialog;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\ElementFactory;
use EzSystems\EzPlatformAdminUi\Behat\PageObject\ObjectStateGroupPage;
use EzSystems\EzPlatformAdminUi\Behat\PageObject\PageObjectFactory;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\AssertionFailedError;

class ObjectStatesContext extends BusinessContext
{
    /**
     * @Then there's :objectStateName on :objectStateGroupName Object States list
     */
    public function verfyObjectStateIsOnList(string $objectStateName, string $objectStateGroupName): void
    {
        $objectStateGroupPage = PageObjectFactory::createPage($this->utilityContext, ObjectStateGroupPage::PAGE_NAME, $objectStateGroupName);
        $objectStateGroupPage->verifyIsLoaded();
        $objectStateExists = $objectStateGroupPage->adminLists['Object States']->table->isElementInTable($objectStateName);

        if (!$objectStateExists) {
            Assert::fail(sprintf('Element "%s" is not on the "%s" object states list.', $objectStateName, $objectStateGroupName));
        }
    }

    /**
     * @Then there's no :objectStateName on :objectStateGroupName Object States list
     */
    public function verfyObjectStateIsNotOnList(string $objectStateName, string $objectStateGroupName): void
    {
        $objectStateGroupPage = PageObjectFactory::createPage($this->utilityContext, ObjectStateGroupPage::PAGE_NAME, $objectStateGroupName);
        $objectStateGroupPage->verifyIsLoaded();
        try {
            $objectStateGroupPage->verifyListIsEmpty();
        } catch (AssertionFailedError $e) {
            $objectStateExists = $objectStateGroupPage->adminLists['Object States']->table->isElementInTable($objectStateName);

            if ($objectStateExists) {
                Assert::fail(sprintf('Element "%s" is on the "%s" object states list.', $objectStateName, $objectStateGroupName));
            }
        }
    }

    /**
     * @Given I go to :objectStateName Object State page from :objectStateGroupName
     */
    public function iGoToObjectState(string $objectStateName, string $objectStateGroupName): void
    {
        $objectStateGroupPage = PageObjectFactory::createPage($this->utilityContext, ObjectStateGroupPage::PAGE_NAME, $objectStateGroupName);
        $objectStateGroupPage->verifyIsLoaded();
        $objectStateGroupPage->adminLists['Object States']->table->clickListElement($objectStateName);
    }

    /**
     * @When I delete Object State from :objectStateGroupName
     */
    public function iDeleteObjecStatesFromGroup(string $objectStateGroupName, TableNode $settings): void
    {
        $hash = $settings->getHash();
        $objectStateGroupPage = PageObjectFactory::createPage($this->utilityContext, ObjectStateGroupPage::PAGE_NAME, $objectStateGroupName);
        foreach ($hash as $setting) {
            $objectStateGroupPage->adminLists['Object States']->table->selectListElement($setting['item']);
        }

        $objectStateGroupPage->adminLists['Object States']->clickTrashButton();
        $dialog = ElementFactory::createElement($this->utilityContext, Dialog::ELEMENT_NAME);
        $dialog->verifyVisibility();
        $dialog->confirm();
    }
}
