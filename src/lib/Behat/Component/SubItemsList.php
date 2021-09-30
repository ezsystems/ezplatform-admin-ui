<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\AdminUi\Behat\Component;

use Behat\Mink\Session;
use Ibexa\AdminUi\Behat\Component\Table\SubitemsGrid;
use Ibexa\AdminUi\Behat\Component\Table\TableBuilder;
use Ibexa\AdminUi\Behat\Component\Table\TableInterface;
use Ibexa\Behat\Browser\Component\Component;
use Ibexa\Behat\Browser\Element\Condition\ElementNotExistsCondition;
use Ibexa\Behat\Browser\Element\Criterion\ElementTextCriterion;
use Ibexa\Behat\Browser\Locator\CSSLocator;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;
use PHPUnit\Framework\Assert;

class SubItemsList extends Component
{
    /** @var \Ibexa\AdminUi\Behat\Component\Table\Table */
    protected $table;

    protected $isGridViewEnabled;

    /** @var \Ibexa\AdminUi\Behat\Component\Table\SubitemsGrid */
    private $grid;

    public function __construct(Session $session, TableBuilder $tableBuilder, SubitemsGrid $grid)
    {
        parent::__construct($session);
        $this->table = $tableBuilder
            ->newTable()
            ->withParentLocator($this->getLocator('table'))
            ->withEmptyLocator($this->getLocator('empty'))
            ->build();
        $this->grid = $grid;
    }

    public function sortBy(string $columnName, bool $ascending): void
    {
        if ($this->isGridViewEnabled) {
            return;
        }

        $header = $this->getHTMLPage()->findAll($this->getLocator('horizontalHeaders'))->getByCriterion(new ElementTextCriterion($columnName));
        $header->mouseOver();
        usleep(100 * 2500); // 250 ms TODO: Remove after redesign
        $header->click();
        $isSortedDescending = $this->getHTMLPage()->findAll($this->getLocator('sortingOrderDescending'))->any();

        if (!$isSortedDescending && !$ascending) {
            $header = $this->getHTMLPage()->findAll($this->getLocator('horizontalHeaders'))->getByCriterion(new ElementTextCriterion($columnName));
            $header->mouseOver();
            usleep(100 * 2500); // 250 ms TODO: Remove after redesign
            $header->click();
        }

        $verificationLocator = $ascending ?
            $this->getLocator('sortingOrderAscending') : $this->getLocator('sortingOrderDescending');

        $this->getHTMLPage()->setTimeout(5)->find($verificationLocator);
    }

    public function shouldHaveGridViewEnabled(bool $enabled): void
    {
        $this->isGridViewEnabled = $enabled;
    }

    public function verifyIsLoaded(): void
    {
        Assert::assertTrue($this->getHTMLPage()->find($this->getLocator('table'))->isVisible());
        $this->getHTMLPage()->setTimeout(5)->waitUntilCondition(
            new ElementNotExistsCondition($this->getHTMLPage(), $this->getLocator('spinner'))
        );
        $this->getHTMLPage()->find($this->getLocator('paginationInfo'))->mouseOver();
        $this->getHTMLPage()
            ->setTimeout(3)
            ->find(new VisibleCSSLocator('paginationInfoVisible', $this->getLocator('paginationInfo')->getSelector()))
            ->assert()->textContains('Viewing');
    }

    public function clickListElement(string $contentName, string $contentType)
    {
        $this->getTable()->getTableRow(['Name' => $contentName, 'Content Type' => $contentType])->goToItem();
    }

    public function isElementInTable(array $elementData): bool
    {
        return $this->getTable()->hasElement($elementData);
    }

    public function goTo(string $itemName): void
    {
        $this->getTable()->getTableRow(['Name' => $itemName])->goToItem();
    }

    protected function getTable(): TableInterface
    {
        return $this->isGridViewEnabled ? $this->grid : $this->table;
    }

    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('table', '.m-sub-items'),
            new VisibleCSSLocator('empty', '.c-no-items'),
            new VisibleCSSLocator('horizontalHeaders', '.m-sub-items .ibexa-table__header-cell'),
            new VisibleCSSLocator('paginationInfo', '.m-sub-items__pagination-info'),
            new CSSLocator('spinner', '.m-sub-items__spinner-wrapper'),
            new CSSLocator('sortingOrderAscending', '.m-sub-items .ibexa-table__header-cell .ibexa-table__sort-column--sorted-asc'),
            new CSSLocator('sortingOrderDescending', '.m-sub-items .ibexa-table__header-cell .ibexa-table__sort-column--desc'),
        ];
    }
}
