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

class ContentTypeGroupsPage extends Page
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

    public function edit(string $contentTypeGroupName): void
    {
        $this->table->getTableRow(['Name' => $contentTypeGroupName])->edit();
    }

    public function delete(string $contentTypeGroupName): void
    {
        $this->table->getTableRow(['Name' => $contentTypeGroupName])->select();
        $this->getHTMLPage()->find($this->getLocator('trashButton'))->click();
        $this->dialog->verifyIsLoaded();
        $this->dialog->confirm();
    }

    public function createNew(): void
    {
        $this->getHTMLPage()->find($this->getLocator('createButton'))->click();
    }

    public function isContentTypeGroupOnTheList(string $contentTypeGroupName): bool
    {
        return $this->table->hasElement(['Name' => $contentTypeGroupName]);
    }

    public function canBeSelected(string $contentTypeGroupName): bool
    {
        return $this->table->getTableRow(['Name' => $contentTypeGroupName])->canBeSelected();
    }

    public function verifyIsLoaded(): void
    {
        Assert::assertEquals(
            'Content Type groups',
            $this->getHTMLPage()->find($this->getLocator('pageTitle'))->getText()
        );
        Assert::assertEquals(
            'Content Type groups',
            $this->getHTMLPage()->find($this->getLocator('listHeader'))->getText()
        );
    }

    public function getName(): string
    {
        return 'Content Type groups';
    }

    protected function getRoute(): string
    {
        return '/contenttypegroup/list';
    }

    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('pageTitle', '.ez-page-title h1'),
            new VisibleCSSLocator('listHeader', '.ibexa-table-header .ibexa-table-header__headline, header .ibexa-table__headline, header h5'),
            new VisibleCSSLocator('createButton', '.ibexa-icon--create'),
            new VisibleCSSLocator('trashButton', '.ibexa-icon--trash,button[data-bs-original-title^="Delete"]'),
        ];
    }
}
