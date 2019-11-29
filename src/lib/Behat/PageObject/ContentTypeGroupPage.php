<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\PageObject;

use EzSystems\EzPlatformAdminUi\Behat\Helper\UtilityContext;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\AdminList;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\ElementFactory;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Tables\IconLinkedListTable;

class ContentTypeGroupPage extends Page
{
    /** @var string Name by which Page is recognised */
    public const PAGE_NAME = 'Content Type Group';
    /** @var string Name of actual group */
    public $groupName;

    /**
     * @var \EzSystems\EzPlatformAdminUi\Behat\PageElement\AdminList
     */
    public $adminList;

    public function __construct(UtilityContext $context, string $groupName)
    {
        parent::__construct($context);
        $this->siteAccess = 'admin';
        $this->route = '/contenttypegroup/';
        $this->groupName = $groupName;
        $this->adminList = ElementFactory::createElement($this->context, AdminList::ELEMENT_NAME, sprintf('Content Types in %s', $this->groupName), IconLinkedListTable::ELEMENT_NAME);
        $this->pageTitle = $groupName;
        $this->pageTitleLocator = '.ez-header h1';
    }

    /**
     * Verifies that all necessary elements are visible.
     */
    public function verifyElements(): void
    {
        $this->adminList->verifyVisibility();
    }

    /**
     * Verifies if lists from given tab is empty.
     *
     * @param string $tabName
     */
    public function verifyListIsEmpty(string $tabName): void
    {
        if ($this->adminList->table->getItemCount() > 0) {
            throw new \Exception(sprintf('%s list is not empty.', $tabName));
        }
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
