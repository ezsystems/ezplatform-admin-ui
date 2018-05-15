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

class DashboardPage extends Page
{
    /** @var string Name by which Page is recognised */
    public const PAGE_NAME = 'Dashboard';

    public const TABLE_CONTAINER = '.card:nth-child(1) .tab-pane.active';

    /** @var NavLinkTabs */
    public $navLinkTabs;

    public $dashboardTable;

    public $fields;

    public function __construct(UtilityContext $context)
    {
        parent::__construct($context);
        $this->route = '/admin/dashboard';
        $this->fields = ['meSection' => '.card-body'];
        $this->pageTitle = 'My dashboard';
        $this->pageTitleLocator = '.ez-header h1';
        $this->navLinkTabs = ElementFactory::createElement($context, NavLinkTabs::ELEMENT_NAME);
        $this->dashboardTable = ElementFactory::createElement($context, DashboardTable::ELEMENT_NAME, $this::TABLE_CONTAINER);
    }

    /**
     * Verifies that the Dashboard has the "Me" section.
     */
    public function verifyElements(): void
    {
        $this->context->waitUntilElementIsVisible($this->fields['meSection']);
        $this->navLinkTabs->verifyVisibility();
        $this->dashboardTable->verifyVisibility();
    }

    public function isListEmpty(): bool
    {
        $tableValue = $this->context->findElement($this::TABLE_CONTAINER)->getText();

        return strpos($tableValue, 'No content items.') !== false;
    }
}
