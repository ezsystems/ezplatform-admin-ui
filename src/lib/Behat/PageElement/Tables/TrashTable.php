<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\PageElement\Tables;

use Behat\Mink\Element\NodeElement;
use EzSystems\EzPlatformAdminUi\Behat\Helper\UtilityContext;

class TrashTable extends Table
{
    /** @var string Name by which Element is recognised */
    public const ELEMENT_NAME = 'Trash Table';

    public function __construct(UtilityContext $context, $containerLocator)
    {
        parent::__construct($context, $containerLocator);
        $this->fields['horizontalHeaders'] = $this->fields['list'] . ' thead th';
        $this->fields['tableTrash'] = '[name=trash_item_restore]';
        $this->fields['listElement'] = $this->fields['list'] . ' tbody td:nth-child(2)';
        $this->fields['checkboxInput'] = ' input';
        $this->fields['trashButton'] = '[id=delete-trash-items]';
        $this->fields['restoreButton'] = '[id=trash_item_restore_restore]';
        $this->fields['restoreUnderNewLocationButton'] = '[id=trash_item_restore_location_select_content]';
    }

    public function verifyVisibility(): void
    {
        $this->context->waitUntilElementIsVisible($this->fields['tableTrash']);
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

    public function clickTrashButton(): void
    {
        $this->context->findElement($this->fields['trashButton'], $this->defaultTimeout)->click();
    }

    public function clickRestoreButton(): void
    {
        $this->context->findElement($this->fields['restoreButton'], $this->defaultTimeout)->click();
    }

    public function clickRestoreUnderNewLocationButton(): void
    {
        $this->context->findElement($this->fields['restoreUnderNewLocationButton'], $this->defaultTimeout)->click();
    }
}
