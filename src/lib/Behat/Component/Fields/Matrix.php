<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\AdminUi\Behat\Component\Fields;

use Ibexa\Behat\Browser\Element\Mapper\ElementTextMapper;
use Ibexa\Behat\Browser\Locator\CSSLocatorBuilder;
use Ibexa\Behat\Browser\Locator\LocatorInterface;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;
use PHPUnit\Framework\Assert;

class Matrix extends FieldTypeComponent
{
    public function setValue(array $parameters): void
    {
        $matrixValues = $this->parseParameters($parameters);

        $availableRows = count($this->getHTMLPage()->findAll($this->getLocator('row')));
        $rowsToSet = count($matrixValues);

        if ($rowsToSet > $availableRows) {
            $this->addRows($rowsToSet - $availableRows);
        }

        foreach ($matrixValues as $rowIndex => $row) {
            foreach ($row as $column => $value) {
                $this->internalSetValue((int) $rowIndex, $column, $value);
            }
        }
    }

    public function getValue(): array
    {
        return [$this->getParsedTableValue($this->getLocator('editModeTableHeaders'), $this->getLocator('editModeTableRow'))];
    }

    public function verifyValueInItemView(array $expectedValue): void
    {
        $parsedTable = $this->getParsedTableValue($this->getLocator('viewModeTableHeaders'), $this->getLocator('viewModeTableRow'));

        Assert::assertEquals($expectedValue['value'], $parsedTable);
    }

    public function getFieldTypeIdentifier(): string
    {
        return 'ezmatrix';
    }

    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('matrixCellSelectorFormat', '[name="ezplatform_content_forms_content_edit[fieldsData][ezmatrix][value][entries][%d][%s]"]'),
            new VisibleCSSLocator('row', '.ez-table__matrix-entry'),
            new VisibleCSSLocator('addRowButton', '.ez-btn--add-matrix-entry'),
            new VisibleCSSLocator('viewModeTableHeaders', 'thead th'),
            new VisibleCSSLocator('viewModeTableRow', 'tbody tr'),
            new VisibleCSSLocator('editModeTableHeaders', '.ez-table thead th[data-identifier]'),
            new VisibleCSSLocator('editModeTableRow', '.ez-table tr.ez-table__matrix-entry'),
        ];
    }

    private function parseParameters(array $parameters): array
    {
        $rows = explode(',', $parameters['value']);

        $columnIdentifiers = explode(':', array_shift($rows));
        $numberOfColumns = count($columnIdentifiers);

        $parsedRows = [];
        foreach ($rows as $row) {
            $parsedRow = [];
            $columnValues = explode(':', $row);
            for ($i = 0; $i < $numberOfColumns; ++$i) {
                $parsedRow[$columnIdentifiers[$i]] = $columnValues[$i];
            }

            $parsedRows[] = $parsedRow;
        }

        return $parsedRows;
    }

    private function addRows(int $numberOfRows): void
    {
        for ($i = 0; $i < $numberOfRows; ++$i) {
            $this->getHTMLPage()->find($this->getLocator('addRowButton'))->click();
        }
    }

    private function internalSetValue(int $rowIndex, string $column, $value): void
    {
        $matrixCellSelector = CSSLocatorBuilder::combine(
            $this->getLocator('matrixCellSelectorFormat')->getSelector(),
            new VisibleCSSLocator('rowIndex', (string) $rowIndex),
            new VisibleCSSLocator('columnIndex', $column),
        );

        $this->getHTMLPage()->find($matrixCellSelector)->setValue($value);
    }

    private function getParsedTableValue(LocatorInterface $headerSelector, LocatorInterface $rowSelector): string
    {
        $parsedTable = '';

        $headers = $this->getHTMLPage()->find($this->parentLocator)->findAll($headerSelector)->mapBy(new ElementTextMapper());

        $parsedTable .= implode(':', $headers);

        $rows = $this->getHTMLPage()->find($this->parentLocator)->findAll($rowSelector);
        foreach ($rows as $row) {
            $parsedTable .= ',';
            $cellValues = $row
                ->findAll(new VisibleCSSLocator('cell', 'td'))
                ->mapBy(new ElementTextMapper())
            ;
            $parsedTable .= implode(':', $cellValues);
        }

        return $parsedTable;
    }
}
