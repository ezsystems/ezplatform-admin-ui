<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\PageObject;

use EzSystems\EzPlatformAdminUi\Behat\Helper\UtilityContext;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\AdminList;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\ElementFactory;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Tables\LinkedListTable;
use PHPUnit\Framework\Assert;

class ObjectStateGroupsPage extends Page
{
    /** @var string Name by which Page is recognised */
    public const PAGE_NAME = 'Object State Groups';

    /**
     * @var \EzSystems\EzPlatformAdminUi\Behat\PageElement\AdminList
     */
    public $adminList;

    public function __construct(UtilityContext $context)
    {
        parent::__construct($context);
        $this->adminList = ElementFactory::createElement($this->context, AdminList::ELEMENT_NAME, self::PAGE_NAME, LinkedListTable::ELEMENT_NAME);
        $this->siteAccess = 'admin';
        $this->route = '/state/groups';
        $this->pageTitle = self::PAGE_NAME;
        $this->pageTitleLocator = '.ez-header h1';
    }

    public function verifyElements(): void
    {
        $this->adminList->verifyVisibility();
    }

    public function verifyItemAttribute(string $label, string $value, string $itemName): void
    {
        Assert::assertEquals(
            $value,
            $this->adminList->table->getTableCellValue($itemName, $label),
            sprintf('Attribute "%s" of item "%s" has wrong value.', $label, $itemName)
        );
    }

    public function startEditingItem(string $itemName): void
    {
        $this->adminList->table->clickEditButton($itemName);
    }

    public function startCreatingItem(): void
    {
        $this->adminList->clickPlusButton();
    }
}
