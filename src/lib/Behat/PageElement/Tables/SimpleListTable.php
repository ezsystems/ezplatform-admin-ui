<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\PageElement\Tables;

use Behat\Mink\Element\NodeElement;
use EzSystems\Behat\Browser\Context\BrowserContext;

class SimpleListTable extends Table
{
    /** @var string Name by which Element is recognised */
    public const ELEMENT_NAME = 'Simple List Table';

    public function __construct(BrowserContext $context, $containerLocator)
    {
        parent::__construct($context, $containerLocator);
        $this->fields['horizontalHeaders'] = $this->fields['list'] . ' .ez-table-header + form thead th';
        $this->fields['listElement'] = $this->fields['list'] . ' .ez-table__cell--has-checkbox+ td.ez-table__cell';
        $this->fields['checkboxInput'] = ' .form-check-input';
    }

    public function getTableCellValue(string $header, ?string $secondHeader = null): string
    {
        $columnPosition = $this->context->getElementPositionByText(
            $header,
            $this->fields['horizontalHeaders']
        );
        $rowPosition = $this->context->getElementPositionByText(
            $secondHeader,
            $this->fields['listElement']
        );

        return $this->getCellValue($rowPosition, $columnPosition);
    }

    /**
     * @return array all table records as hash map
     */
    public function getTableHash(): array
    {
        $tableHash = [];

        /** @var NodeElement[] $allHeaders */
        $allHeaders = $this->context->findAllElements($this->fields['horizontalHeaders']);
        /** @var NodeElement[] $allRows */
        $allRows = $this->context->findAllElements($this->fields['listRow']);
        $j = 0;
        foreach ($allRows as $row) {
            $rowHash = [];
            /** @var NodeElement[] $allCells */
            $allCells = $row->findAll('css', 'td');
            $i = 0;
            foreach ($allCells as $cell) {
                $rowHash[$allHeaders[$i]->getText()] = $cell->getText();
                ++$i;
            }
            $tableHash[$j] = $rowHash;
            ++$j;
        }

        return $tableHash;
    }

    /**
     * Check checkbox left to link element with given name.
     *
     * @param string $name
     */
    public function selectListElement(string $name): void
    {
        $this->selectElement($name, $this->fields['listElement']);
    }

    public function clickEditButton(string $listItemName): void
    {
        $this->clickEditButtonByElementLocator($listItemName, $this->fields['listElement']);
    }
}
