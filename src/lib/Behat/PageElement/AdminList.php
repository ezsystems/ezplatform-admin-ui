<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\PageElement;

use Behat\Mink\Exception\ElementNotFoundException;
use EzSystems\EzPlatformAdminUi\Behat\Helper\UtilityContext;

/** Element that describes list-table structure that repeats in every Admin pages */
class AdminList extends Element
{
    /** @var string Name by which Element is recognised */
    public const ELEMENT_NAME = 'Admin List';
    /** @var string list table title placed in the blue bar */
    protected $listHeader;

    public function __construct(UtilityContext $context, string $listHeader)
    {
        parent::__construct($context);
        $this->listHeader = $listHeader;
        $this->fields = [
            'list' => 'section',
            'listHeader' => '.ez-table-header__headline, header h5',
            'plusButton' => '.ez-icon-create',
            'trashButton' => '.ez-icon-trash',
            'editButton' => 'tr:nth-child(%s) a[title=Edit]',
            'listElementLink' => '.ez-checkbox-cell+ td a',
            'tableCell' => 'tr:nth-child(%d) td:nth-child(%d)',
            'checkboxInput' => ' .form-check-input',
            'verticalHeaders' => 'colgroup+ tbody th',
            'insideHeaders' => 'thead+ tbody th',
            'horizontalHeaders' => '.ez-table-header + .table thead th, .ez-table-header + form thead th',
        ];
    }

    public function verifyVisibility(): void
    {
        $actualHeader = $this->context->getElementByText($this->listHeader, $this->fields['listHeader']);
        if ($actualHeader === null) {
            throw new ElementNotFoundException($this->context->getSession(), 'table header', $this->fields['listHeader']);
        }
    }

    public function clickPlusButton(): void
    {
        $this->context->findElement($this->fields['plusButton'])->click();
    }

    public function clickTrashButton(): void
    {
        $this->context->findElement($this->fields['trashButton'])->click();
    }

    public function clickEditButton(string $listItemName): void
    {
        $position = $this->context->getElementPositionByText($listItemName, $this->fields['listElementLink']);
        $this->context->findElement(sprintf($this->fields['editButton'], $position))->click();
    }

    /**
     * Check if list contains link element with given name.
     *
     * @param string $name
     *
     * @return bool
     */
    public function isLinkElementOnList(string $name): bool
    {
        return $this->context->getElementByText($name, $this->fields['listElementLink']) !== null;
    }

    /**
     * Check if checkbox left to link element if active.
     *
     * @param string $name
     *
     * @return bool 'true' for enabled and 'false' for disabled
     */
    public function isLinkElementSelectable(string $name): bool
    {
        $position = $this->context->getElementPositionByText($name, $this->fields['listElementLink']);
        $checkbox = $this->context->findElement(sprintf($this->fields['tableCell'], $position, 1) . $this->fields['checkboxInput'])->getAttribute('disabled');

        return $checkbox !== 'disabled';
    }

    /**
     * Check checkbox left to link element with given name.
     *
     * @param string $name
     */
    public function selectListElement(string $name): void
    {
        $position = $this->context->getElementPositionByText($name, $this->fields['listElementLink']);
        $this->context->findElement(sprintf($this->fields['tableCell'], $position, 1))->checkField('');
    }

    /**
     * Click link element with given name.
     *
     * @param string $name
     */
    public function clickListElement(string $name): void
    {
        $this->context->getElementByText($name, $this->fields['listElementLink'])->click();
    }

    /**
     * Get value of cell with given coordinates.
     *
     * @param int $row
     * @param int $column
     *
     * @return string
     *
     * @throws \Exception when coordinates are invalid
     */
    public function getCellValue(int $row, int $column): string
    {
        $cell = $this->context->findElement(sprintf($this->fields['tableCell'], $row, $column));

        if ($cell !== null) {
            if (strpos($cell->getHtml(), 'type="checkbox"') !== false) {
                return strpos($cell->getHtml(), 'checked') ? 'true' : 'false';
            }

            return $cell->getText();
        }

        throw new \Exception('Cell coordinates not valid - row %d, column %d', $row, $column);
    }

    /**
     * Getting attribute of link elements with given name, from column with given header.
     *
     * @param string $name
     * @param string $header
     *
     * @return string
     */
    public function getListItemAttribute(string $name, string $header): string
    {
        $columnPosition = $this->context->getElementPositionByText(
            $header,
            $this->fields['horizontalHeaders'],
            null,
            $this->context->findElement($this->fields['list'])
        );
        $rowPosition = $this->context->getElementPositionByText(
            $name,
            $this->fields['listElementLink'],
            null,
            $this->context->findElement($this->fields['list'])
        );

        return $this->getCellValue($rowPosition, $columnPosition);
    }

    /**
     * Getting attributes of list which is vertical oriented table,
     * like in Content Type details view General information.
     *
     * @param string $header
     *
     * @return string
     */
    public function getCellValueFromVerticalOrientedTable(string $header): string
    {
        $rowPosition = $this->context->getElementPositionByText(
            $header,
            $this->fields['verticalHeaders'],
            null,
            $this->context->findElement($this->fields['list'])
        );

        return $this->getCellValue($rowPosition, 2);
    }

    /**
     * Getting attributes of list which has both - horizontal and vertical headers,
     * like in Content Type details view Content fields information.
     *
     * @param string $columnHeader
     * @param string $rowHeader
     *
     * @return string
     */
    public function getCellValueFromDoubleHeaderTable(string $columnHeader, string $rowHeader): string
    {
        $columnPosition = $this->context->getElementPositionByText(
            $columnHeader,
            $this->fields['horizontalHeaders']
        );
        $rowPosition = $this->context->getElementPositionByText(
            $rowHeader,
            $this->fields['insideHeaders']
        );

        return $this->getCellValue($rowPosition, $columnPosition);
    }

    /**
     * Getting attributes of list which has only horizontal headers, and no links elements
     * like in Languages details view.
     *
     * @param string $columnHeader
     *
     * @return string
     */
    public function getCellValueFromSimpleTable(string $columnHeader): string
    {
        $columnPosition = $this->context->getElementPositionByText(
            $columnHeader,
            $this->fields['horizontalHeaders']
        );

        return $this->getCellValue(1, $columnPosition);
    }
}
