<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\PageElement\Fields;

use Behat\Mink\Element\NodeElement;
use EzSystems\Behat\Browser\Context\BrowserContext;
use PHPUnit\Framework\Assert;

class Matrix extends EzFieldElement
{
    /** @var string Name by which Element is recognised */
    public const ELEMENT_NAME = 'Matrix';

    public function __construct(BrowserContext $context, string $locator, string $label)
    {
        parent::__construct($context, $locator, $label);
        $this->fields['matrixCellSelectorFormat'] = '[name="ezrepoforms_content_edit[fieldsData][ezmatrix][value][entries][%d][%s]"]';
        $this->fields['row'] = '.ez-table__matrix-entry';
        $this->fields['addRowButton'] = '.ez-btn--add-matrix-entry';
        $this->fields['viewModeTableHeaders'] = '.ez-content-field-value thead th';
        $this->fields['viewModeTableRow'] = '.ez-content-field-value tbody tr';
        $this->fields['editModeTableHeaders'] = '.ez-table thead th[data-identifier]';
        $this->fields['editModeTableRow'] = '.ez-table tr.ez-table__matrix-entry';
    }

    public function setValue(array $parameters): void
    {
        $matrixValues = $this->parseParameters($parameters);

        $availableRows = count($this->context->findAllElements($this->fields['row']));
        $rowsToSet = count($matrixValues);

        if ($rowsToSet > $availableRows) {
            $this->addRows($rowsToSet - $availableRows);
        }

        foreach ($matrixValues as $rowIndex => $row) {
            foreach ($row as $column => $value) {
                $this->internalSetValue((int)$rowIndex, $column, $value);
            }
        }
    }

    public function getValue(): array
    {
        return [$this->getParsedTableValue($this->fields['editModeTableHeaders'], $this->fields['editModeTableRow'])];
    }

    public function verifyValueInItemView(array $expectedValue): void
    {
        $parsedTable = $this->getParsedTableValue($this->fields['viewModeTableHeaders'], $this->fields['viewModeTableRow']);

        Assert::assertEquals($expectedValue['value'], $parsedTable);
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
            $this->context->findElement($this->fields['addRowButton'])->click();
        }
    }

    private function internalSetValue(int $rowIndex, string $column, $value)
    {
        $this->context->findElement(sprintf($this->fields['matrixCellSelectorFormat'], $rowIndex, $column))->setValue($value);
    }

    private function getParsedTableValue($headerSelector, $rowSelector)
    {
        $parsedTable = '';

        $headerElements = $this->context->findAllElements($headerSelector);
        $headers = array_map(function (NodeElement $element) { return $element->getText(); }, $headerElements);

        $parsedTable .= implode(':', $headers);

        $rows = $this->context->findAllElements($rowSelector);
        foreach ($rows as $row) {
            $parsedTable .= ',';
            $cells = $row->findAll('css', 'td');
            $cellValues = array_map(function (NodeElement $element) { return $element->getText();}, $cells);
            $parsedTable .= implode(':', $cellValues);
        }

        return $parsedTable;
    }
}
