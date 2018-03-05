<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\PageObject;


use EzSystems\EzPlatformAdminUi\Behat\Helper\UtilityContext;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\AdminList;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Dialog;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Element;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\ElementFactory;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\NavLinkTabs;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\SimpleListTable;
use WebDriver\Exception;

class RolePage extends Page
{
    /** @var string Name by which Page is recognised */
    public const PAGE_NAME = 'Role';
    /** @var string Name of actual group */
    public $roleName;

    private $activeAdminListContainerLocator = '.ez-main-container .tab-pane.active';

    /**
     * @var \EzSystems\EzPlatformAdminUi\Behat\PageElement\AdminList[]
     */
    public $adminLists;

    /**
     * @var \EzSystems\EzPlatformAdminUi\Behat\PageElement\AdminList
     */
    public $adminList;

    /**
     * @var \EzSystems\EzPlatformAdminUi\Behat\PageElement\Dialog[]
     */
    public $dialogs;

    /**
     * @var AdminList|\EzSystems\EzPlatformAdminUi\Behat\PageElement\NavLinkTabs
     */
    public $navLinkTabs;

    public function __construct(UtilityContext $context, string $roleName)
    {
        parent::__construct($context);
        $this->route = '/admin/role/';
        $this->roleName = $roleName;
        $this->adminLists['Policies'] = ElementFactory::createElement($this->context, AdminList::ELEMENT_NAME, 'Policies', SimpleListTable::ELEMENT_NAME, $this->activeAdminListContainerLocator);
        $this->adminLists['Assignments'] = ElementFactory::createElement($this->context, AdminList::ELEMENT_NAME, 'Users and Groups', SimpleListTable::ELEMENT_NAME, $this->activeAdminListContainerLocator);
        $this->adminList = ElementFactory::createElement($this->context, AdminList::ELEMENT_NAME, '', SimpleListTable::ELEMENT_NAME, $this->activeAdminListContainerLocator);
        $this->navLinkTabs = ElementFactory::createElement($this->context, NavLinkTabs::ELEMENT_NAME);
        $this->dialogs['Policies'] = ElementFactory::createElement($this->context, Dialog::ELEMENT_NAME, $this->activeAdminListContainerLocator);
        $this->dialogs['Assignments'] = ElementFactory::createElement($this->context, Dialog::ELEMENT_NAME, $this->activeAdminListContainerLocator);
        $this->pageTitle = sprintf('Role "%s"', $roleName);
        $this->pageTitleLocator = '.ez-header h1';
    }

    /**
     * Verifies that all necessary elements are visible.
     */
    public function verifyElements(): void
    {
        $this->navLinkTabs->verifyVisibility();
        $this->adminLists['Policies']->verifyVisibility();
        $this->navLinkTabs->goToTab('Assignments');
        $this->adminLists['Assignments']->verifyVisibility();
    }

    /**
     * Verifies if lists from given tab is empty
     * @param string $tabName
     */
    public function verifyListIsEmpty(string $tabName): void
    {
        $this->navLinkTabs->goToTab($tabName);
        if($this->adminLists[$tabName]->table->getItemCount() > 0){
            throw new \Exception(sprintf('"%s" list is not empty.', $tabName));
        }
    }

}