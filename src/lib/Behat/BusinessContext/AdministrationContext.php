<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\BusinessContext;

use Behat\Gherkin\Node\TableNode;
use EzSystems\Behat\Browser\Page\Page;
use EzSystems\Behat\Browser\Factory\ElementFactory;
use EzSystems\Behat\Browser\Factory\PageObjectFactory;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Dialog;
use EzSystems\EzPlatformAdminUi\Behat\PageObject\ObjectStateGroupPage;
use EzSystems\EzPlatformAdminUi\Behat\PageObject\ObjectStateGroupsPage;
use EzSystems\EzPlatformAdminUi\Behat\PageObject\RolePage;
use EzSystems\EzPlatformAdminUi\Behat\PageObject\RolesPage;
use EzSystems\EzPlatformAdminUi\Behat\PageObject\ContentTypeGroupPage;
use EzSystems\EzPlatformAdminUi\Behat\PageObject\ContentTypeGroupsPage;
use EzSystems\EzPlatformAdminUi\Behat\PageObject\LanguagesPage;
use EzSystems\EzPlatformAdminUi\Behat\PageObject\SectionPage;
use EzSystems\EzPlatformAdminUi\Behat\PageObject\SectionsPage;
use PHPUnit\Framework\Assert;

/** Context for common actions (creating, editing, deleting, etc) in Admin pages (Content Types, Languages, etc.) */
class AdministrationContext extends BusinessContext
{
    private $itemCreateMapping = [
        'Content Type Group' => ContentTypeGroupsPage::PAGE_NAME,
        'Content Type' => ContentTypeGroupPage::PAGE_NAME,
        'Language' => LanguagesPage::PAGE_NAME,
        'Role' => RolesPage::PAGE_NAME,
        'Limitation' => RolePage::PAGE_NAME,
        'Policy' => RolePage::PAGE_NAME,
        'Section' => SectionsPage::PAGE_NAME,
        'Object State Group' => ObjectStateGroupsPage::PAGE_NAME,
        'Object State' => ObjectStateGroupPage::PAGE_NAME,
        'User' => '',
    ];
    private $emptyHeaderMapping = [
        'Content Type Groups' => 'Content Types count',
        'Sections' => 'Assignments count',
    ];

    /**
     * @Then I should see :pageName list
     * @Then I should see :pageName :parameter list
     *
     * @param string $pageName
     */
    public function iSeeList(string $pageName, string $parameter = null): void
    {
        $contentTypeGroupsPage = PageObjectFactory::createPage($this->browserContext, $pageName, $parameter);
        $contentTypeGroupsPage->verifyElements();
    }

    /**
     * @When I start creating new :newItemType
     * @When I start creating new :newItemType in :containerItem
     *
     * @param string $newItemType
     */
    public function iStartCreatingNew(string $newItemType, ?string $containerItem = null): void
    {
        if (!array_key_exists($newItemType, $this->itemCreateMapping)) {
            throw new \InvalidArgumentException(sprintf('Unrecognized item type name: %s', $newItemType));
        }
        PageObjectFactory::createPage($this->browserContext, $this->itemCreateMapping[$newItemType], $containerItem)
            ->startCreatingItem();
    }

    /**
     * @When I start assigning to :itemName from :pageType page
     */
    public function iStartAssigningTo(string $itemName, string $pageType): void
    {
        $pageObject = PageObjectFactory::createPage($this->browserContext, $pageType, $itemName);
        $pageObject->startAssigningToItem($itemName);
    }

    /**
     * @Then there's :listElementName on :page list
     * @Then there's :listElementName on :parameter :page list
     */
    public function verifyElementOnTheList(string $listElementName, string $page, ?string $parameter = null): void
    {
        $pageElement = PageObjectFactory::createPage($this->browserContext, $page, $parameter);
        if (!$pageElement->adminList->isElementOnTheList($listElementName)) {
            Assert::fail(sprintf('Element "%s" is on the %s list.', $listElementName, $page));
        }
    }

    /**
     * @Then there's no :listElementName on :page list
     * @Then there's no :listElementName on :parameter :page list
     */
    public function verifyElementNotOnTheList(string $listElementName, string $page, string $parameter = null): void
    {
        $pageElement = PageObjectFactory::createPage($this->browserContext, $page, $parameter);
        if ($pageElement->adminList->isElementOnTheList($listElementName)) {
            Assert::fail(sprintf('Element "%s" is on the %s list.', $listElementName, $page));
        }
    }

    /**
     * Check if item is or is not empty, according to $empty param.
     *
     * @param string $itemName
     * @param string $page
     * @param bool $shouldBeEmpty
     */
    private function verifyContentsStatus(string $itemName, string $page, bool $shouldBeEmpty): void
    {
        $emptyContainerCellValue = '0';

        $contentsCount = PageObjectFactory::createPage($this->browserContext, $page)
            ->adminList->table->getTableCellValue($itemName, $this->emptyHeaderMapping[$page]);

        $msg = '';
        if ($shouldBeEmpty) {
            $msg = ' non';
        }

        if (($contentsCount !== $emptyContainerCellValue) === $shouldBeEmpty) {
            Assert::fail(sprintf('No%s empty %s on the %s list.', $msg, $itemName, $page));
        }
    }

    /**
     * @Given there's empty :itemName on :page list
     */
    public function isEmptyElementOnTheList(string $itemName, string $page): void
    {
        $this->verifyContentsStatus($itemName, $page, true);
    }

    /**
     * @Given there's non-empty :itemName on :page list
     */
    public function isNonEmptyElementOnTheList(string $itemName, string $page): void
    {
        $this->verifyContentsStatus($itemName, $page, false);
    }

    /**
     * @Then :itemType :itemName cannot be selected
     */
    public function itemCannotBeSelected(string $itemType, string $itemName): void
    {
        $isListElementSelectable = PageObjectFactory::createPage($this->browserContext, $this->itemCreateMapping[$itemType])
            ->adminList->table->isElementSelectable($itemName);

        if ($isListElementSelectable) {
            Assert::fail(sprintf('Element %s shoudn\'t be selectable.', $itemName));
        }
    }

    /**
     * @Given I go to :itemName :itemType page
     * @Given I go to :itemName :itemType page from :itemContainer
     */
    public function iGoToListItem(string $itemName, string $itemType, string $itemContainer = null): void
    {
        $pageElement = PageObjectFactory::createPage($this->browserContext, $this->itemCreateMapping[$itemType], $itemContainer);
        if ($pageElement->adminList->isElementOnTheList($itemName)) {
            $pageElement->adminList->table->clickListElement($itemName);
        } else {
            Assert::fail(sprintf('Element %s is not on the list.', $itemName));
        }
    }

    /**
     * @When I start editing :itemType :itemName
     * @When I start editing :itemType :itemName from :containerName
     */
    public function iStartEditingItem(string $itemType, string $itemName, ?string $containerName = null): void
    {
        PageObjectFactory::createPage($this->browserContext, $this->itemCreateMapping[$itemType], $containerName)
            ->startEditingItem($itemName);
    }

    /**
     * @When I start editing :itemType :itemName from details page
     */
    public function iStartEditingItemFromDetails(string $itemType, string $itemName): void
    {
        PageObjectFactory::createPage($this->browserContext, $itemType, $itemName)
            ->startEditingSelf($itemName);
    }

    /**
     * @When I delete :itemType
     */
    public function iDeleteItems(string $itemType, TableNode $settings): void
    {
        $hash = $settings->getHash();

        $page = PageObjectFactory::createPage($this->browserContext, $this->itemCreateMapping[$itemType]);
        foreach ($hash as $setting) {
            $page->adminList->table->selectListElement($setting['item']);
        }

        $this->performDeletion($page);
    }

    /**
     * @When I delete :itemType from :containerName
     */
    public function iDeleteItemsFromContainer(string $itemType, ?string $containerName = null, TableNode $settings): void
    {
        $hash = $settings->getHash();

        $page = PageObjectFactory::createPage($this->browserContext, $this->itemCreateMapping[$itemType], $containerName);
        foreach ($hash as $setting) {
            $page->adminList->table->selectListElement($setting['item']);
        }

        $this->performDeletion($page);
    }

    /**
     * @When I delete :itemType from details page
     */
    public function iDeleteItemsFromDetails(string $itemType, TableNode $settings): void
    {
        $hash = $settings->getHash();

        $page = PageObjectFactory::createPage($this->browserContext, $itemType, $hash[0]['item']);
        $this->performDeletion($page);
    }

    /**
     * @param ContentTypeGroupsPage|LanguagesPage|RolePage|RolesPage|SectionPage|SectionsPage $page
     */
    private function performDeletion(Page $page)
    {
        $page->adminList->clickTrashButton();
        $dialog = ElementFactory::createElement($this->browserContext, Dialog::ELEMENT_NAME);
        $dialog->verifyVisibility();
        $dialog->confirm();
    }

    /**
     * @Then :itemType :itemName has attribute :attributeName set to :value
     */
    public function itemHasProperAttribute(string $itemType, string $itemName, string $attributeName, string $value)
    {
        $pageObject = PageObjectFactory::createPage($this->browserContext, $itemType, $itemName);

        $pageObject->verifyItemAttribute($attributeName, $value);
    }

    /**
     * @When :itemName on :pageName list has attribute :attributeName set to :value
     */
    public function linkItemHasProperAttribute(string $itemName, string $pageName, string $attributeName, string $value)
    {
        $pageObject = PageObjectFactory::createPage($this->browserContext, $pageName);
        $pageObject->verifyItemAttribute($attributeName, $value, $itemName);
    }

    /**
     * @Then :itemType :itemName has proper attributes
     */
    public function itemHasProperAttributes(string $itemType, string $itemName, TableNode $settings)
    {
        $hash = $settings->getHash();
        foreach ($hash as $setting) {
            $this->itemHasProperAttribute($itemType, $itemName, $setting['label'], $setting['value']);
        }
    }

    /**
     * @Then :listName list in :itemType :itemName is empty
     */
    public function listIsEmpty(string $listName, string $itemType, string $itemName): void
    {
        $pageObject = PageObjectFactory::createPage($this->browserContext, $itemType, $itemName);
        $pageObject->verifyListIsEmpty($listName);
    }
}
