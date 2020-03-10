<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\PageObject;

use EzSystems\EzPlatformAdminUi\Behat\Helper\UtilityContext;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\AdminList;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\ElementFactory;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\NavLinkTabs;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Tables\SimpleTable;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Tables\SystemInfoTable;
use PHPUnit\Framework\Assert;

class SystemInfoPage extends Page
{
    public const PAGE_NAME = 'System Information';

    /**
     * @var AdminList[]
     */
    public $adminLists;

    /**
     * @var NavLinkTabs
     */
    public $navLinkTabs;

    /**
     * @var SystemInfoTable
     */
    public $systemInfoTable;

    public function __construct(UtilityContext $context)
    {
        parent::__construct($context);
        $this->adminLists['Packages'] = ElementFactory::createElement($this->context, AdminList::ELEMENT_NAME, 'Packages', SimpleTable::ELEMENT_NAME, '.ez-main-container .tab-pane.active');
        $this->adminLists['Bundles'] = ElementFactory::createElement($this->context, AdminList::ELEMENT_NAME, 'Bundles', SimpleTable::ELEMENT_NAME, '.ez-main-container .tab-pane.active');
        $this->systemInfoTable = ElementFactory::createElement($context, SystemInfoTable::ELEMENT_NAME, '.ez-main-container .tab-pane.active .ez-table--list');
        $this->navLinkTabs = ElementFactory::createElement($context, NavLinkTabs::ELEMENT_NAME);
        $this->siteAccess = 'admin';
        $this->route = '/systeminfo';
        $this->pageTitle = self::PAGE_NAME;
        $this->pageTitleLocator = '.ez-header h1';
    }

    public function verifyElements(): void
    {
        $this->navLinkTabs->verifyVisibility();
        $this->adminLists['Packages']->verifyVisibility();
    }

    public function verifySystemInfoTable(string $tabName): void
    {
        $this->systemInfoTable->verifyHeader($tabName);
    }

    public function verifySystemInfoRecords(string $tableName, array $records): void
    {
        $this->adminLists[$tableName]->verifyVisibility();
        $tableHash = $this->adminLists[$tableName]->table->getTableHash();

        foreach ($records as $desiredRecord) {
            $found = false;
            foreach ($tableHash as $actualRecord) {
                if ($desiredRecord['Name'] === $actualRecord['Name']) {
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                Assert::fail(sprintf('Desired record [%s] not found in "%s" list.', $desiredRecord['Name'], $tableName));
            }
        }
    }
}
