<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\BusinessContext;

use EzSystems\EzPlatformAdminUi\Behat\PageElement\Dialog;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\ElementFactory;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\LeftMenu;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\RightMenu;
use EzSystems\EzPlatformAdminUi\Behat\PageObject\PageObjectFactory;
use EzSystems\EzPlatformAdminUi\Behat\PageObject\TrashPage;
use PHPUnit\Framework\Assert;

class TrashContext extends BusinessContext
{
    /**
     * @Then trash is empty
     */
    public function trashIsEmpty(): void
    {
        $trash = PageObjectFactory::createPage($this->utilityContext, TrashPage::PAGE_NAME);
        Assert::assertTrue(
            $trash->isTrashEmpty(),
            'Trash is not empty.'
        );
    }

    /**
     * @When I empty the trash
     */
    public function iEmptyTrash(): void
    {
        $rightMenu = ElementFactory::createElement($this->utilityContext, RightMenu::ELEMENT_NAME);
        $rightMenu->clickButton('Empty Trash');
        $dialog = ElementFactory::createElement($this->utilityContext, Dialog::ELEMENT_NAME);
        $dialog->confirm();
    }

    /**
     * @Then going to trash there is :itemType :itemName on list
     */
    public function goingToTrashThereIsItemOnList(string $itemType, string $itemName): void
    {
        $leftMenu = ElementFactory::createElement($this->utilityContext, LeftMenu::ELEMENT_NAME);
        $leftMenu->clickButton('Trash');

        $trash = PageObjectFactory::createPage($this->utilityContext, TrashPage::PAGE_NAME);
        $trash->verifyIfItemInTrash($itemType, $itemName);
    }
}
