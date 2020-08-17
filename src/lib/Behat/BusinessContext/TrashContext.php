<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\BusinessContext;

use Behat\Gherkin\Node\TableNode;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Dialog;
use EzSystems\Behat\Browser\Factory\ElementFactory;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\LeftMenu;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\RightMenu;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\UniversalDiscoveryWidget;
use EzSystems\Behat\Core\Environment\EnvironmentConstants;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\UpperMenu;
use EzSystems\Behat\Browser\Factory\PageObjectFactory;
use EzSystems\EzPlatformAdminUi\Behat\PageObject\TrashPage;
use EzSystems\EzPlatformAdminUi\Behat\PageObject\ContentItemPage;
use PHPUnit\Framework\Assert;

class TrashContext extends BusinessContext
{
    /**
     * @Then trash is empty
     */
    public function trashIsEmpty(): void
    {
        $trash = PageObjectFactory::createPage($this->browserContext, TrashPage::PAGE_NAME);
        Assert::assertTrue(
            $trash->isTrashEmpty(),
            'Trash is not empty.'
        );
    }

    /**
     * @When trash is not empty
     */
    public function trashIsNotEmpty(): void
    {
        $trash = PageObjectFactory::createPage($this->browserContext, TrashPage::PAGE_NAME);
        Assert::assertFalse(
            $trash->isTrashEmpty(),
            'Trash is empty.'
        );
    }

    /**
     * @When I empty the trash
     */
    public function iEmptyTrash(): void
    {
        $rightMenu = ElementFactory::createElement($this->browserContext, RightMenu::ELEMENT_NAME);
        $rightMenu->clickButton('Empty Trash');
        $dialog = ElementFactory::createElement($this->browserContext, Dialog::ELEMENT_NAME);
        $dialog->confirm();
    }

    /**
     * @Then going to trash there is :itemType :itemName on list
     */
    public function goingToTrashThereIsItemOnList(string $itemType, string $itemName): void
    {
        $leftMenu = ElementFactory::createElement($this->browserContext, LeftMenu::ELEMENT_NAME);

        if (!$leftMenu->isVisible()) {
            // we're not in Content View
            $upperMenu = ElementFactory::createElement($this->browserContext, UpperMenu::ELEMENT_NAME);
            $upperMenu->goToTab('Content');
            $upperMenu->goToSubTab('Content structure');

            $contentPage = PageObjectFactory::createPage($this->browserContext, ContentItemPage::PAGE_NAME, EnvironmentConstants::get('ROOT_CONTENT_NAME'));
            $contentPage->verifyIsLoaded();
        }

        $leftMenu->clickButton('Trash');

        $trash = PageObjectFactory::createPage($this->browserContext, TrashPage::PAGE_NAME);
        $trash->verifyIfItemInTrash($itemType, $itemName, true);
    }

    /**
     * @When I delete item from trash list
     */
    public function iDeleteItemFromTrash(TableNode $itemsTable): void
    {
        $trashPage = PageObjectFactory::createPage($this->browserContext, TrashPage::PAGE_NAME);

        foreach ($itemsTable->getHash() as $itemTable) {
            $trashPage->trashTable->selectListElement($itemTable['item']);
        }

        $trashPage->trashTable->clickTrashButton();
        $trashPage->dialog->verifyVisibility();
        $trashPage->dialog->confirm();
    }

    /**
     * @When I restore item from trash
     */
    public function iRestoreItemFromTrash(TableNode $itemsTable): void
    {
        $trashPage = PageObjectFactory::createPage($this->browserContext, TrashPage::PAGE_NAME);

        foreach ($itemsTable->getHash() as $itemTable) {
            $trashPage->trashTable->selectListElement($itemTable['item']);
        }

        $trashPage->trashTable->clickRestoreButton();
    }

    /**
     * @When I restore item from trash under new location :pathToContent
     */
    public function iRestoreItemFromTrashUnderNewLocation(TableNode $itemsTable, string $pathToContent): void
    {
        $trashPage = PageObjectFactory::createPage($this->browserContext, TrashPage::PAGE_NAME);

        foreach ($itemsTable->getHash() as $itemTable) {
            $trashPage->trashTable->selectListElement($itemTable['item']);
        }

        $trashPage->trashTable->clickRestoreUnderNewLocationButton();
        $udw = ElementFactory::createElement($this->browserContext, UniversalDiscoveryWidget::ELEMENT_NAME);
        $udw->verifyVisibility();
        $udw->selectContent($pathToContent);
        $udw->confirm();
    }

    /**
     * @Then there is :itemType :itemName on trash list
     */
    public function thereIsItemOnTrashList(string $itemType, string $itemName): void
    {
        $trashPage = PageObjectFactory::createPage($this->browserContext, TrashPage::PAGE_NAME);
        $trashPage->verifyIfItemInTrash($itemType, $itemName, true);
    }

    /**
     * @Then there is no :itemType :itemName on trash list
     */
    public function thereIsNoItemOnTrashList(string $itemType, string $itemName): void
    {
        $trashPage = PageObjectFactory::createPage($this->browserContext, TrashPage::PAGE_NAME);
        $trashPage->verifyIfItemInTrash($itemType, $itemName, false);
    }
}
