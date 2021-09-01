<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\AdminUi\Behat\BrowserContext;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Ibexa\AdminUi\Behat\Page\TrashPage;
use PHPUnit\Framework\Assert;

class TrashContext implements Context
{
    /** @var \Ibexa\AdminUi\Behat\Page\TrashPage */
    private $trashPage;

    public function __construct(TrashPage $trashPage)
    {
        $this->trashPage = $trashPage;
    }

    /**
     * @Then trash is empty
     */
    public function trashIsEmpty(): void
    {
        Assert::assertTrue(
            $this->trashPage->isEmpty(),
            'Trash is not empty.'
        );
    }

    /**
     * @When trash is not empty
     */
    public function trashIsNotEmpty(): void
    {
        Assert::assertFalse(
            $this->trashPage->isEmpty(),
            'Trash is empty.'
        );
    }

    /**
     * @When I empty the trash
     */
    public function iEmptyTrash(): void
    {
        $this->trashPage->emptyTrash();
    }

    /**
     * @When I delete item from trash list
     */
    public function iDeleteItemFromTrash(TableNode $itemsTable): void
    {
        foreach ($itemsTable->getHash() as $itemTable) {
            $this->trashPage->select(['Name' => $itemTable['item']]);
        }

        $this->trashPage->deleteSelectedItems();
    }

    /**
     * @When I restore item from trash
     */
    public function iRestoreItemFromTrash(TableNode $itemsTable): void
    {
        foreach ($itemsTable->getHash() as $itemTable) {
            $this->trashPage->select(['Name' => $itemTable['item']]);
        }

        $this->trashPage->restoreSelectedItems();
    }

    /**
     * @When I restore item from trash under new location :pathToContent
     */
    public function iRestoreItemFromTrashUnderNewLocation(TableNode $itemsTable, string $pathToContent): void
    {
        foreach ($itemsTable->getHash() as $itemTable) {
            $this->trashPage->select(['Name' => $itemTable['item']]);
        }

        $this->trashPage->restoreSelectedNewLocation($pathToContent);
    }

    /**
     * @Then there is a :itemType :itemName on Trash list
     */
    public function thereIsItemOnTrashList(string $itemType, string $itemName): void
    {
        Assert::assertTrue($this->trashPage->hasElement($itemType, $itemName));
    }

    /**
     * @Then there is no :itemType :itemName on Trash list
     */
    public function thereIsNoItemOnTrashList(string $itemType, string $itemName): void
    {
        Assert::assertFalse($this->trashPage->hasElement($itemType, $itemName));
    }
}
