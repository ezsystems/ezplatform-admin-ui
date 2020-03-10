<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\PageObject;

use EzSystems\EzPlatformAdminUi\Behat\Helper\UtilityContext;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\AdminList;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\ElementFactory;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Tables\SimpleTable;
use PHPUnit\Framework\Assert;

class ObjectStatePage extends Page
{
    /** @var string Name by which Page is recognised */
    public const PAGE_NAME = 'Object State';
    /** @var string $languageName */
    private $objectStateName;

    /**
     * @var \EzSystems\EzPlatformAdminUi\Behat\PageElement\AdminList
     */
    public $adminList;

    public function __construct(UtilityContext $context, string $objectStateName)
    {
        parent::__construct($context);
        $this->adminList = ElementFactory::createElement($this->context, AdminList::ELEMENT_NAME, self::PAGE_NAME . ' Information', SimpleTable::ELEMENT_NAME);
        $this->objectStateName = $objectStateName;
        $this->siteAccess = 'admin';
        $this->route = '/state/state';
        $this->pageTitle = sprintf('Object State: %s', $objectStateName);
        $this->pageTitleLocator = '.ez-header h1';
    }

    public function verifyElements(): void
    {
        $this->adminList->verifyVisibility();
    }

    public function startEditingSelf(string $itemName): void
    {
        $this->adminList->table->clickEditButton($itemName);
    }

    public function verifyItemAttribute(string $label, string $value): void
    {
        Assert::assertEquals(
            $value,
            $this->adminList->table->getTableCellValue($label),
            sprintf('Attribute "%s" has wrong value.', $label)
        );
    }
}
