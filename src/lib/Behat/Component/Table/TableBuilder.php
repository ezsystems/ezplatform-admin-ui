<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\AdminUi\Behat\Component\Table;

use Behat\Mink\Session;
use eZ\Publish\Core\Base\Exceptions\BadStateException;
use Ibexa\Behat\Browser\Locator\CSSLocator;
use Ibexa\Behat\Browser\Locator\LocatorCollection;
use Ibexa\AdminUi\Behat\Component\Pagination;

class TableBuilder
{
    /** @var \Behat\Mink\Session */
    private $session;

    /** @var \Ibexa\AdminUi\Behat\Component\Table\TableRowFactory */
    private $tableRowFactory;

    /** @var \Ibexa\AdminUi\Behat\Component\Pagination */
    private $pagination;

    /** @var \Ibexa\Behat\Browser\Locator\LocatorCollection */
    private $locators;
    /**
     * @var bool
     */
    private $buildInProgress;

    public function __construct(Session $session, TableRowFactory $tableRowFactory, Pagination $pagination)
    {
        $this->session = $session;
        $this->tableRowFactory = $tableRowFactory;
        $this->pagination = $pagination;
        $this->locators = new LocatorCollection([]);
    }

    public function newTable(): self
    {
        if ($this->buildInProgress) {
            throw new BadStateException('buildInProgress', 'A Table building process is already in progress. Please finish it before starting a new one.');
        }

        $this->buildInProgress = true;

        $this->locators = new LocatorCollection([
            new CSSLocator('empty', '.ez-table__empty-table-cell'),
            new CSSLocator('columnHeader', '.ez-table__header-cell,th'),
            new CSSLocator('row', 'tr'),
            new CSSLocator('cell', '.ez-table__cell:nth-of-type(%d),td:nth-of-type(%d)'),
            new CSSLocator('parent', '.ez-table'),
        ]);

        return $this;
    }

    public function build(): TableInterface
    {
        if (!$this->buildInProgress) {
            throw new BadStateException('buildInProgress', 'Please call "newTable()" before building a Table object');
        }

        $this->buildInProgress = false;

        return new Table($this->session, $this->tableRowFactory, $this->pagination, $this->locators);
    }

    public function withRowLocator(CSSLocator $locator): self
    {
        $rowLocator = new CSSLocator('row', $locator->getSelector());
        $this->locators->replace($rowLocator);

        return $this;
    }

    public function withTableCell(CSSLocator $locator): self
    {
        $rowLocator = new CSSLocator('cell', $locator->getSelector());
        $this->locators->replace($rowLocator);

        return $this;
    }

    public function withParentLocator(CSSLocator $locator): self
    {
        $parentLocator = new CSSLocator('parent', $locator->getSelector());
        $this->locators->replace($parentLocator);

        return $this;
    }

    public function withEmptyLocator(CSSLocator $locator): self
    {
        $emptyLocator = new CSSLocator('empty', $locator->getSelector());
        $this->locators->replace($emptyLocator);

        return $this;
    }

    public function withColumnLocator(CSSLocator $locator): self
    {
        $columnLocator = new CSSLocator('columnHeader', $locator->getSelector());
        $this->locators->replace($columnLocator);

        return $this;
    }
}
