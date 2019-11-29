<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\PageObject;

use EzSystems\EzPlatformAdminUi\Behat\Helper\UtilityContext;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Dialog;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\ElementFactory;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Tables\TrashTable;
use PHPUnit\Framework\Assert;

class TrashPage extends Page
{
    /** @var string Name by which Page is recognised */
    public const PAGE_NAME = 'Trash';

    public const ITEM_RESTORE_LIST_CONTAINER = '[name=trash_item_restore]';

    /**
     * @var \EzSystems\EzPlatformAdminUi\Behat\PageElement\Dialog
     */
    public $dialog;

    /**
     * @var \EzSystems\EzPlatformAdminUi\Behat\PageElement\Tables\TrashTable
     */
    public $trashTable;

    public function __construct(UtilityContext $context)
    {
        parent::__construct($context);
        $this->siteAccess = 'admin';
        $this->route = '/trash/list';
        $this->trashTable = ElementFactory::createElement($this->context, TrashTable::ELEMENT_NAME, $this::ITEM_RESTORE_LIST_CONTAINER);
        $this->dialog = ElementFactory::createElement($this->context, Dialog::ELEMENT_NAME);
        $this->pageTitle = 'Trash';
        $this->pageTitleLocator = '.ez-page-title h1';
    }

    /**
     * Verifies that all necessary elements are visible.
     */
    public function verifyElements(): void
    {
        $this->trashTable->verifyVisibility();
    }

    public function verifyIfItemInTrash(string $itemType, string $itemName, bool $elementShouldExist): void
    {
        $isElementInTrash = !$this->isTrashEmpty() &&
            ($this->trashTable->isElementInTable($itemName) && $this->trashTable->getTableCellValue('Type', $itemName) == $itemType);
        $elementShouldExistString = $elementShouldExist ? 'n\'t' : '';

        Assert::assertTrue(
            ($isElementInTrash == $elementShouldExist),
            sprintf('Item %s %s is%s in trash', $itemType, $itemName, $elementShouldExistString)
        );
    }

    public function isTrashEmpty(): bool
    {
        $firstRowValue = $this->trashTable->getCellValue(1, 1);

        return $this->trashTable->getItemCount() === 1 && strpos($firstRowValue, 'Trash is empty.') !== false;
    }
}
