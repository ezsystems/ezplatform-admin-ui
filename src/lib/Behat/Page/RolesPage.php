<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\AdminUi\Behat\Page;

use Behat\Mink\Session;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;
use Ibexa\Behat\Browser\Page\Page;
use Ibexa\Behat\Browser\Routing\Router;
use Ibexa\AdminUi\Behat\Component\Dialog;
use Ibexa\AdminUi\Behat\Component\Table\TableBuilder;
use PHPUnit\Framework\Assert;

class RolesPage extends Page
{
    /** @var \Ibexa\AdminUi\Behat\Component\Table\Table */
    private $table;

    /** @var \Ibexa\AdminUi\Behat\Component\Dialog */
    private $dialog;

    public function __construct(Session $session, Router $router, TableBuilder $tableBuilder, Dialog $dialog)
    {
        parent::__construct($session, $router);
        $this->table = $tableBuilder->newTable()->build();
        $this->dialog = $dialog;
    }

    public function verifyItemAttribute(string $label, string $value, string $itemName): void
    {
        Assert::assertEquals(
            $value,
            $this->adminList->table->getTableCellValue($itemName, $label),
            sprintf('Attribute "%s" of item "%s" has wrong value.', $label, $itemName)
        );
    }

    public function create(): void
    {
        $this->getHTMLPage()->find($this->getLocator('createButton'))->click();
    }

    public function isRoleOnTheList(string $roleName): bool
    {
        return $this->table->hasElement(['Name' => $roleName]);
    }

    public function editRole(string $roleName): void
    {
        $this->table->getTableRow(['Name' => $roleName])->edit();
    }

    public function startAssinging(string $roleName): void
    {
        $this->table->getTableRow(['Name' => $roleName])->assign();
    }

    public function deleteRole(string $roleName)
    {
        $this->table->getTableRow(['Name' => $roleName])->select();
        $this->getHTMLPage()->find($this->getLocator('deleteRoleButton'))->click();
        $this->dialog->verifyIsLoaded();
        $this->dialog->confirm();
    }

    public function getName(): string
    {
        return 'Roles';
    }

    public function verifyIsLoaded(): void
    {
        Assert::assertEquals(
            'Roles',
            $this->getHTMLPage()->find($this->getLocator('pageTitle'))->getText()
        );
    }

    protected function getRoute(): string
    {
        return '/role/list';
    }

    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('createButton', '.ibexa-icon--create'),
            new VisibleCSSLocator('pageTitle', '.ez-page-title h1'),
            new VisibleCSSLocator('deleteRoleButton', '#delete-roles'),
        ];
    }
}
