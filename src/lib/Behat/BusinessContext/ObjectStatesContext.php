<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\BusinessContext;

use Behat\Gherkin\Node\TableNode;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Dialog;
use EzSystems\Behat\Browser\Factory\ElementFactory;
use EzSystems\EzPlatformAdminUi\Behat\PageObject\ObjectStateGroupPage;
use EzSystems\Behat\Browser\Factory\PageObjectFactory;
use PHPUnit\Framework\Assert;

class ObjectStatesContext extends BusinessContext
{
    /**
     * @Then there's :objectStateName on :objectStateGroupName Object States list
     */
    public function verfyObjectStateIsOnList(string $objectStateName, string $objectStateGroupName): void
    {
        $objectStateGroupPage = PageObjectFactory::createPage($this->browserContext, ObjectStateGroupPage::PAGE_NAME, $objectStateGroupName);
        $objectStateGroupPage->verifyIsLoaded();
        $objectStateExists = $objectStateGroupPage->adminLists['Object states']->table->isElementOnCurrentPage($objectStateName);

        Assert::assertTrue(
            $objectStateExists,
            sprintf('Element "%s" is not on the "%s" Object state list.', $objectStateName, $objectStateGroupName)
        );
    }

    /**
     * @Then there's no :objectStateName on :objectStateGroupName Object States list
     */
    public function verifyObjectStateIsNotOnList(string $objectStateName, string $objectStateGroupName): void
    {
        $objectStateGroupPage = PageObjectFactory::createPage($this->browserContext, ObjectStateGroupPage::PAGE_NAME, $objectStateGroupName);
        $objectStateGroupPage->verifyIsLoaded();
        if (!$objectStateGroupPage->isListEmpty('Object states')) {
            $objectStateExists = $objectStateGroupPage->adminLists['Object states']->table->isElementOnCurrentPage($objectStateName);

            Assert::assertFalse(
                $objectStateExists,
                sprintf('Element "%s" is on the "%s" Object state list.', $objectStateName, $objectStateGroupName)
            );
        }
    }

    /**
     * @Given I go to :objectStateName Object State page from :objectStateGroupName
     */
    public function iGoToObjectState(string $objectStateName, string $objectStateGroupName): void
    {
        $objectStateGroupPage = PageObjectFactory::createPage($this->browserContext, ObjectStateGroupPage::PAGE_NAME, $objectStateGroupName);
        $objectStateGroupPage->verifyIsLoaded();
        $objectStateGroupPage->adminLists['Object states']->table->clickListElement($objectStateName);
    }

    /**
     * @When I delete Object State from :objectStateGroupName
     */
    public function iDeleteObjecStatesFromGroup(string $objectStateGroupName, TableNode $settings): void
    {
        $hash = $settings->getHash();
        $objectStateGroupPage = PageObjectFactory::createPage($this->browserContext, ObjectStateGroupPage::PAGE_NAME, $objectStateGroupName);
        foreach ($hash as $setting) {
            $objectStateGroupPage->adminLists['Object states']->table->selectListElement($setting['item']);
        }

        $objectStateGroupPage->adminLists['Object states']->clickTrashButton();
        $dialog = ElementFactory::createElement($this->browserContext, Dialog::ELEMENT_NAME);
        $dialog->verifyVisibility();
        $dialog->confirm();
    }
}
