<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\PageObject;

use EzSystems\EzPlatformAdminUi\Behat\Helper\UtilityContext;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\AdminList;
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
     * @var \EzSystems\EzPlatformAdminUi\Behat\PageElement\AdminList
     */
    public $adminList;

    /**
     * @var \EzSystems\EzPlatformAdminUi\Behat\PageElement\Dialog[]
     */
    public $dialog;

    public function __construct(UtilityContext $context)
    {
        parent::__construct($context);
        $this->route = '/admin/trash/list';
        $this->adminList = ElementFactory::createElement($this->context, AdminList::ELEMENT_NAME, 'Trash', TrashTable::ELEMENT_NAME, $this::ITEM_RESTORE_LIST_CONTAINER);
        $this->dialog = ElementFactory::createElement($this->context, Dialog::ELEMENT_NAME);
        $this->pageTitle = 'Trash';
        $this->pageTitleLocator = '.ez-page-title h1';
    }

    /**
     * Verifies that all necessary elements are visible.
     */
    public function verifyElements(): void
    {
        $this->adminList->verifyVisibility();
    }

    public function verifyIfItemInTrash(string $itemType, string $itemName): void
    {
        Assert::assertFalse(
            $this->isTrashEmpty() ||
            !$this->adminList->table->isElementInTable($itemName) ||
            ($this->adminList->table->isElementInTable($itemName) && $this->adminList->table->getTableCellValue('Type', $itemName) !== $itemType),
            sprintf('Item %s %s is not in trash', $itemType, $itemName)
        );
    }

    public function isTrashEmpty(): bool
    {
        $firstRowValue = $this->adminList->table->getCellValue(1, 1);

        return $this->adminList->table->getItemCount() === 1 && strpos($firstRowValue, 'Trash is empty.') !== false;
    }
}
