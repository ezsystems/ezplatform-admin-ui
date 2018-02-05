<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\BusinessContext;

use Behat\Mink\Exception\ElementNotFoundException;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Dialog;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\ElementFactory;
use EzSystems\EzPlatformAdminUi\Behat\PageObject\ContentTypeGroupsPage;
use EzSystems\EzPlatformAdminUi\Behat\PageObject\PageObjectFactory;

class AdministrationContext extends BusinessContext
{
    private $itemCreateMapping = ['Content Type Group' => ContentTypeGroupsPage::PAGE_NAME,
                        'Content Type' => '',
                        'Language' => '',
                        'Role' => '',
                        'Section' => '',
                        'User' => '', ];
    private $emptyHeaderMapping = ['Content Type Groups' => 'Content Types count',
                            'Sections' => 'Assigned Content items', ];

    /**
     * @Then I should see :pageName list
     * @Then I should see :pageName :parameter list
     *
     * @param string $pageName
     */
    public function iSeeList(string $pageName, string $parameter = null): void
    {
        $contentTypeGroupsPage = PageObjectFactory::createPage($this->utilityContext, $pageName, $parameter);
        $contentTypeGroupsPage->verifyElements();
    }

    /**
     * @When I start creating new :newItemType
     *
     * @param string $newItemType
     */
    public function iStartCreatingNew(string $newItemType): void
    {
        if (!\array_key_exists($newItemType, $this->itemCreateMapping)) {
            throw new \InvalidArgumentException(sprintf('Unrecognised item type name: %s', $newItemType));
        }
        PageObjectFactory::createPage($this->utilityContext, $this->itemCreateMapping[$newItemType])
            ->adminList->clickPlusButton();
    }

    /**
     * @Then there's :listElementName on :page list
     * @Then there's :listElementName on :parameter :page list
     */
    public function isElementOnTheList($listElementName, $page): void
    {
        $actual = PageObjectFactory::createPage($this->utilityContext, $page)
            ->adminList->isElementOnList($listElementName);

        if (!$actual) {
            throw new ElementNotFoundException(
                    $this->utilityContext->getSession(),
                    sprintf('Element "%s" is not on the %s list.', $listElementName, $page));
        }
    }

    /**
     * @Then there's no :listElementName on :page list
     * @Then there's no :listElementName on :parameter :page list
     */
    public function isElementNotOnTheList($listElementName, $page, $parameter = null): void
    {
        $actual = PageObjectFactory::createPage($this->utilityContext, $page, $parameter)
            ->adminList->isElementOnList($listElementName);

        if ($actual) {
            throw new ElementNotFoundException(
                $this->utilityContext->getSession(),
                sprintf('Element "%s" is on the %s list.', $listElementName, $page));
        }
    }

    /**
     * Check if item is or is not empty, according to $empty param.
     *
     * @param $itemName
     * @param $page
     * @param $shouldBeEmpty
     */
    private function verifyContentsStatus($itemName, $page, $shouldBeEmpty): void
    {
        $isEmpty = '0';

        $contentsCount = PageObjectFactory::createPage($this->utilityContext, $page)
            ->adminList->getListItemAttribute($itemName, $this->emptyHeaderMapping[$page]);

        $msg = '';
        if ($shouldBeEmpty) {
            $msg = ' non';
        }

        if (($contentsCount !== $isEmpty) === $shouldBeEmpty) {
            throw new ElementNotFoundException(
                $this->utilityContext->getSession(),
                sprintf('No%s empty %s on the %s list.', $msg, $itemName, $page));
        }
    }

    /**
     * @Given there's empty :itemName on :page list
     */
    public function isEmptyElementOnTheList($itemName, $page): void
    {
        $this->verifyContentsStatus($itemName, $page, true);
    }

    /**
     * @Given there's non-empty :itemName on :page list
     */
    public function isNonEmptyElementOnTheList($itemName, $page): void
    {
        $this->verifyContentsStatus($itemName, $page, false);
    }

    /**
     * @Then :itemType :itemName cannot be selected
     */
    public function itemCannotBeSelected($itemType, $itemName): void
    {
        $checkboxState = PageObjectFactory::createPage($this->utilityContext, $this->itemCreateMapping[$itemType])
            ->adminList->isListElementSelectable($itemName);

        if ($checkboxState) {
            throw new \Exception(sprintf('Element %s shoudn\'t be selectable.', $itemName));
        }
    }

    /**
     * @Given I go to :itemName :itemType page
     */
    public function iGoToListItem($itemName, $itemType)
    {
        PageObjectFactory::createPage($this->utilityContext, $this->itemCreateMapping[$itemType])
            ->adminList->clickListElement($itemName);
    }

    /**
     * @When I start editing :itemType :itemName
     */
    public function iStartEditingItem($itemType, $itemName)
    {
        PageObjectFactory::createPage($this->utilityContext, $this->itemCreateMapping[$itemType])
            ->adminList->clickEditButton($itemName);
    }

    /**
     * @When I delete :itemType :itemName
     */
    public function iDeleteItem($itemType, $itemName)
    {
        $contentTypeGroups = PageObjectFactory::createPage($this->utilityContext, $this->itemCreateMapping[$itemType]);
        $contentTypeGroups->adminList->selectListElement($itemName);
        $contentTypeGroups->adminList->clickTrashButton();
        ElementFactory::createElement($this->utilityContext, Dialog::ELEMENT_NAME)
            ->confirm();
    }
}
