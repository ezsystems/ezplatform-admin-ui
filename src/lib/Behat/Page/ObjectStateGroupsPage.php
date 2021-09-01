<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\AdminUi\Behat\Page;

use Behat\Mink\Session;
use Ibexa\AdminUi\Behat\Component\Dialog;
use Ibexa\AdminUi\Behat\Component\Table\TableBuilder;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;
use Ibexa\Behat\Browser\Page\Page;
use Ibexa\Behat\Browser\Routing\Router;
use PHPUnit\Framework\Assert;

class ObjectStateGroupsPage extends Page
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

    public function isObjectStateGroupOnTheList(string $objectStateGroupName): bool
    {
        return $this->table->hasElement(['Object state group name' => $objectStateGroupName]);
    }

    public function editObjectStateGroup(string $objectStateGroupName)
    {
        $this->table->getTableRow(['Object state group name' => $objectStateGroupName])->edit();
    }

    public function createObjectStateGroup()
    {
        $this->getHTMLPage()->find($this->getLocator('createButton'))->click();
    }

    public function deleteObjectStateGroup(string $objectStateGroupName)
    {
        $this->table->getTableRow(['Object state group name' => $objectStateGroupName])->select();
        $this->getHTMLPage()->find($this->getLocator('deleteButton'))->click();
        $this->dialog->verifyIsLoaded();
        $this->dialog->confirm();
    }

    public function verifyIsLoaded(): void
    {
        Assert::assertEquals(
            'Object state groups',
            $this->getHTMLPage()->find($this->getLocator('pageTitle'))->getText()
        );
    }

    public function getName(): string
    {
        return 'Object State groups';
    }

    protected function getRoute(): string
    {
        return '/state/groups';
    }

    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('pageTitle', '.ez-page-title h1'),
            new VisibleCSSLocator('createButton', '.ibexa-icon--create'),
            new VisibleCSSLocator('deleteButton', '#delete-object-state-groups'),
        ];
    }
}
