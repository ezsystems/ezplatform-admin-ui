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

class SectionsPage extends Page
{
    /** @var \Ibexa\AdminUi\Behat\Component\Table\TableInterface */
    private $table;

    /** @var \Ibexa\AdminUi\Behat\Component\Dialog */
    private $dialog;

    public function __construct(Session $session, Router $router, TableBuilder $tableBuilder, Dialog $dialog)
    {
        parent::__construct($session, $router);
        $this->table = $tableBuilder->newTable()->withParentLocator($this->getLocator('tableContainer'))->build();
        $this->dialog = $dialog;
    }

    public function createNew(): void
    {
        $this->getHTMLPage()->find($this->getLocator('createButton'))->click();
    }

    public function isSectionOnTheList(string $sectionName): bool
    {
        return $this->table->hasElement(['Name' => $sectionName]);
    }

    public function assignContentItems(string $sectionName)
    {
        $this->table->getTableRow(['Name' => $sectionName])->assign();
    }

    public function getAssignedContentItemsCount(string $sectionName): int
    {
        return (int) $this->table->getTableRow(['Name' => $sectionName])->getCellValue('Assigned content');
    }

    public function editSection(string $sectionName)
    {
        $this->table->getTableRow(['Name' => $sectionName])->edit();
    }

    public function canBeSelected(string $sectionName): bool
    {
        return $this->table->getTableRow(['Name' => $sectionName])->canBeSelected();
    }

    public function deleteSection(string $sectionName)
    {
        $this->table->getTableRow(['Name' => $sectionName])->select();
        $this->getHTMLPage()->find($this->getLocator('deleteButton'))->click();
        $this->dialog->verifyIsLoaded();
        $this->dialog->confirm();
    }

    public function getName(): string
    {
        return 'Sections';
    }

    public function verifyIsLoaded(): void
    {
        Assert::assertEquals(
            'Sections',
            $this->getHTMLPage()->find($this->getLocator('pageTitle'))->getText()
        );
    }

    protected function getRoute(): string
    {
        return '/section/list';
    }

    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('pageTitle', '.ez-page-title h1'),
            new VisibleCSSLocator('createButton', '.ibexa-icon--create'),
            new VisibleCSSLocator('deleteButton', '.ibexa-icon--trash,button[data-bs-original-title^="Delete"]'),
            new VisibleCSSLocator('tableContainer', '.ez-container'),
        ];
    }
}
