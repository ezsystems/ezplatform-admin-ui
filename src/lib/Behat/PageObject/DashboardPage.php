<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\PageObject;

use EzSystems\EzPlatformAdminUi\Behat\Helper\UtilityContext;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\ElementFactory;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\NavLinkTabs;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Tables\DashboardTable;
use PHPUnit\Framework\Assert;

class DashboardPage extends Page
{
    /** @var string Name by which Page is recognised */
    public const PAGE_NAME = 'Dashboard';

    public const TABLE_CONTAINER = '#ez-tab-list-content-dashboard-my .tab-pane.active';

    /** @var NavLinkTabs */
    public $navLinkTabs;

    public $dashboardTable;

    public $fields;

    public function __construct(UtilityContext $context)
    {
        parent::__construct($context);
        $this->siteAccess = 'admin';
        $this->route = '/dashboard';
        $this->fields = [
            'tableSelector' => '.card-body',
            'tableTitle' => '.mb-3',
            'tableTabSelector' => '.ez-tabs .nav-item',
        ];
        $this->pageTitle = 'My dashboard';
        $this->pageTitleLocator = '.ez-header h1';
        $this->navLinkTabs = ElementFactory::createElement($context, NavLinkTabs::ELEMENT_NAME);
        $this->dashboardTable = ElementFactory::createElement($context, DashboardTable::ELEMENT_NAME, $this::TABLE_CONTAINER);
    }

    public function switchTab(string $tableName, string $tabName)
    {
        $table = $this->context->getElementByText('Me', $this->fields['tableSelector'], $this->fields['tableTitle']);
        $this->context->getElementByText($tabName, $this->fields['tableTabSelector'], null, $table)->click();
    }

    /**
     * Verifies that the Dashboard has the "Me" section.
     */
    public function verifyElements(): void
    {
        Assert::assertNotNull($this->context->getElementByText('Me', $this->fields['tableSelector'], $this->fields['tableTitle']));
        $this->navLinkTabs->verifyVisibility();
        $this->dashboardTable->verifyVisibility();
    }

    public function isListEmpty(): bool
    {
        $tableValue = $this->context->findElement($this::TABLE_CONTAINER)->getText();

        return strpos($tableValue, 'No content items.') !== false;
    }
}
