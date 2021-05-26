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
use EzSystems\EzPlatformAdminUi\Behat\PageElement\NavLinkTabs;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Tables\SimpleTable;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Tables\SystemInfoTable;
use PHPUnit\Framework\Assert;

class SystemInfoPage extends Page
{
    public const PAGE_NAME = 'System Information';

    /**
     * @var NavLinkTabs
     */
    public $navLinkTabs;

    /**
     * @var SystemInfoTable
     */
    public $systemInfoTable;

    public function __construct(BrowserContext $context)
    {
        parent::__construct($context);
        $this->navLinkTabs = ElementFactory::createElement($context, NavLinkTabs::ELEMENT_NAME);
        $this->siteAccess = 'admin';
        $this->route = '/systeminfo';
        $this->pageTitle = self::PAGE_NAME;
        $this->pageTitleLocator = '.ez-page-title h1';
    }

    public function verifyElements(): void
    {
        $this->navLinkTabs->verifyVisibility();
        $this->verifySystemInfoTable('Product');
    }

    public function verifySystemInfoTable(string $tabName): void
    {
        $systemInfoTable = ElementFactory::createElement($this->context, SystemInfoTable::ELEMENT_NAME, '.ibexa-main-container .active .ez-fieldgroup:nth-of-type(1)');
        $systemInfoTable->verifyHeader($tabName);
    }

    public function verifySystemInfoRecords(string $tableName, array $records): void
    {
        $tab = ElementFactory::createElement($this->context, AdminList::ELEMENT_NAME, $tableName, SimpleTable::ELEMENT_NAME, '.ibexa-main-container .tab-pane.active');
        $tab->verifyVisibility();
        $tableHash = $tab->table->getTableHash();

        foreach ($records as $desiredRecord) {
            $found = false;
            foreach ($tableHash as $actualRecord) {
                if ($desiredRecord['Name'] === $actualRecord['Name']) {
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                Assert::fail(sprintf('Could not find requested record [%s] on the "%s" list.', $desiredRecord['Name'], $tableName));
            }
        }
    }
}
