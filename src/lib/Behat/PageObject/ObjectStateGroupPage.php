<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\PageObject;

use EzSystems\Behat\Browser\Context\BrowserContext;
use EzSystems\Behat\Browser\Page\Page;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\AdminList;
use EzSystems\Behat\Browser\Factory\ElementFactory;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Tables\LinkedListTable;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Tables\SimpleTable;
use PHPUnit\Framework\Assert;

class ObjectStateGroupPage extends Page
{
    /** @var string Name by which Page is recognised */
    public const PAGE_NAME = 'Object state group';
    /** @var string Name of actual group */
    public $objectStateGroupName;

    /** @var string locator for container of Object States list */
    public $secondListContainerLocator = 'section:nth-of-type(2)';

    /**
     * @var \EzSystems\EzPlatformAdminUi\Behat\PageElement\AdminList[]
     */
    public $adminLists;

    /**
     * @var \EzSystems\EzPlatformAdminUi\Behat\PageElement\AdminList
     */
    public $adminList;

    public function __construct(BrowserContext $context, string $objectStateGroupName)
    {
        parent::__construct($context);
        $this->siteAccess = 'admin';
        $this->route = '/state/group/';
        $this->objectStateGroupName = $objectStateGroupName;
        $this->adminLists['Object state group information'] = ElementFactory::createElement($this->context, AdminList::ELEMENT_NAME, 'Object state group information', SimpleTable::ELEMENT_NAME);
        $this->adminLists['Object states'] = ElementFactory::createElement($this->context, AdminList::ELEMENT_NAME, 'Object states', LinkedListTable::ELEMENT_NAME, $this->secondListContainerLocator);
        $this->adminList = ElementFactory::createElement($this->context, AdminList::ELEMENT_NAME, '', SimpleTable::ELEMENT_NAME);
        $this->pageTitle = sprintf('Object state group: %s', $objectStateGroupName);
        $this->pageTitleLocator = '.ez-header h1';
    }

    /**
     * Verifies that all necessary elements are visible.
     */
    public function verifyElements(): void
    {
        $this->adminLists['Object state group information']->verifyVisibility();
        $this->adminLists['Object states']->verifyVisibility();
    }

    /**
     * Verifies if list of Object States is empty.
     *
     * @param string $listName
     */
    public function verifyListIsEmpty($listName): void
    {
        Assert::assertTrue(
            $this->isListEmpty($listName),
            '"Object States" list is not empty.'
        );
    }

    public function isListEmpty(string $listName): bool
    {
        $firstRowValue = $this->adminLists[$listName]->table->getCellValue(1, 1);

        return $this->adminLists[$listName]->table->getItemCount() === 1 &&
            strpos($firstRowValue, 'There are no Object states yet.') !== false;
    }

    public function startEditingItem(string $itemName): void
    {
        $this->adminLists['Object states']->table->clickEditButton($itemName);
    }

    public function startEditingSelf(string $itemName): void
    {
        $this->adminLists['Object state group information']->table->clickEditButton($itemName);
    }

    public function startCreatingItem(): void
    {
        $this->adminLists['Object states']->clickPlusButton();
    }

    public function verifyItemAttribute(string $label, string $value): void
    {
        Assert::assertEquals(
            $value,
            $this->adminLists['Object state group information']->table->getTableCellValue($label),
            sprintf('Attribute "%s" has wrong value.', $label)
        );
    }
}
