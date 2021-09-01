<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\AdminUi\Behat\BrowserContext;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Ibexa\AdminUi\Behat\Page\ObjectStateGroupPage;
use Ibexa\AdminUi\Behat\Page\ObjectStateGroupsPage;
use Ibexa\AdminUi\Behat\Page\ObjectStatePage;
use PHPUnit\Framework\Assert;

class ObjectStatesContext implements Context
{
    /** @var \Ibexa\AdminUi\Behat\Page\ObjectStateGroupPage */
    private $objectStateGroupPage;

    /** @var \Ibexa\AdminUi\Behat\Page\ObjectStateGroupsPage */
    private $objectStateGroupsPage;

    /** @var \Ibexa\AdminUi\Behat\Page\ObjectStatePage */
    private $objectStatePage;

    public function __construct(
        ObjectStateGroupPage $objectStateGroupPage,
        ObjectStateGroupsPage $objectStateGroupsPage,
        ObjectStatePage $objectStatePage
    ) {
        $this->objectStateGroupPage = $objectStateGroupPage;
        $this->objectStateGroupsPage = $objectStateGroupsPage;
        $this->objectStatePage = $objectStatePage;
    }

    /**
     * @Then there's a :objectStateGroupName Object State group on Object State groups list
     */
    public function isObjectStateOnTheList(string $objectStateGroupName): void
    {
        Assert::assertTrue($this->objectStateGroupsPage->isObjectStateGroupOnTheList($objectStateGroupName));
    }

    /**
     * @Then I should be on :objectStateGroupName Object State group page
     */
    public function iShouldBeOnRObjectStateGroupPage(string $objectStateGroupName)
    {
        $this->objectStateGroupPage->setExpectedObjectStateGroupName($objectStateGroupName);
        $this->objectStateGroupPage->verifyIsLoaded();
    }

    /**
     * @Then I should be on :objectState Object State page
     */
    public function iShouldBeOnObjectStatePage(string $objectStateGroup)
    {
        $this->objectStatePage->setExpectedObjectStateName($objectStateGroup);
        $this->objectStatePage->verifyIsLoaded();
    }

    /**
     * @Then :objectStateGroupName Object State group has no Object States
     */
    public function objectStateGroupIsEmpty(string $objectStateGroupName)
    {
        $this->objectStateGroupPage->setExpectedObjectStateGroupName($objectStateGroupName);
        Assert::assertFalse($this->objectStateGroupPage->hasObjectStates());
    }

    /**
     * @Then Object State group has proper attributes
     */
    public function objectStateGroupHasAttributes(TableNode $table): void
    {
        foreach ($table->getHash() as $row) {
            Assert::assertTrue($this->objectStateGroupPage->hasAttribute($row['label'], $row['value']));
        }
    }

    /**
     * @Then Object State has proper attributes
     */
    public function objectStateHasAttributes(TableNode $table): void
    {
        foreach ($table->getHash() as $row) {
            Assert::assertTrue($this->objectStatePage->hasAttribute($row['label'], $row['value']));
        }
    }

    /**
     * @Then there's no :objectStateName Object State group on Object State groups list
     */
    public function noObjectStateOnTheList(string $objectStateGroupName): void
    {
        Assert::assertFalse($this->objectStateGroupsPage->isObjectStateGroupOnTheList($objectStateGroupName));
    }

    /**
     * @Then I edit :objectStateGroupName from Object State groups list
     */
    public function editObjectStateGroupFromList(string $objectStateGroupName)
    {
        $this->objectStateGroupsPage->editObjectStateGroup($objectStateGroupName);
    }

    /**
     * @Then I start editing Object State :objectStateName from Object State Group
     */
    public function editObjectStateFromList(string $objectStateName)
    {
        $this->objectStateGroupPage->editObjectState($objectStateName);
    }

    /**
     * @Then I edit the Object State
     */
    public function editObjectState()
    {
        $this->objectStatePage->edit();
    }

    /**
     * @Then I edit the Object State group
     */
    public function editObjectStateGroup()
    {
        $this->objectStateGroupPage->edit();
    }

    /**
     * @Then there's no :objectStateName Object State on Object States list for :objectStateGroupName
     */
    public function thereIsNoObjectStateOnTheList(string $objectStateName, string $objectStateGroupName): void
    {
        $this->objectStateGroupPage->setExpectedObjectStateGroupName($objectStateGroupName);
        $this->objectStateGroupPage->verifyIsLoaded();

        Assert::assertFalse(
            $this->objectStateGroupPage->hasObjectState($objectStateName),
        );
    }

    /**
     * @Then there's a :objectStateName Object State on Object States list for :objectStateGroupName
     */
    public function thereIsObjectStateOnTheList(string $objectStateName, string $objectStateGroupName): void
    {
        $this->objectStateGroupPage->setExpectedObjectStateGroupName($objectStateGroupName);
        $this->objectStateGroupPage->verifyIsLoaded();

        Assert::assertTrue(
            $this->objectStateGroupPage->hasObjectState($objectStateName),
        );
    }

    /**
     * @Given I create a new Object State group
     */
    public function createObjectStateGroup(): void
    {
        $this->objectStateGroupsPage->createObjectStateGroup();
    }

    /**
     * @Given I create a new Object State
     */
    public function createObjectState(): void
    {
        $this->objectStateGroupPage->createObjectState();
    }

    /**
     * @When I delete Object State :objectStateName
     */
    public function iDeleteObjecState(string $objectStateName): void
    {
        $this->objectStateGroupPage->deleteObjectState($objectStateName);
    }

    /**
     * @When I delete Object State group :objectStateGroupName
     */
    public function iDeleteObjectStatesGroup(string $objectStateGroupName): void
    {
        $this->objectStateGroupsPage->deleteObjectStateGroup($objectStateGroupName);
    }

    /**
     * @Then I open :objectStateGroupName Object State group page in admin SiteAccess
     */
    public function openObjectStateGroupPage(string $objectStateGroupName)
    {
        $this->objectStateGroupPage->setExpectedObjectStateGroupName($objectStateGroupName);
        $this->objectStateGroupPage->open('admin');
        $this->objectStateGroupPage->verifyIsLoaded();
    }

    /**
     * @Then I open :objectStateName Object State page in admin SiteAccess
     */
    public function openObjectStatePage(string $objectStateName)
    {
        $this->objectStatePage->setExpectedObjectStateName($objectStateName);
        $this->objectStatePage->open('admin');
        $this->objectStatePage->verifyIsLoaded();
    }
}
