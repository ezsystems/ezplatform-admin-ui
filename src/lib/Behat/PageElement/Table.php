<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\PageElement;

use EzSystems\EzPlatformAdminUi\Behat\Helper\UtilityContext;

abstract class Table extends Element
{
    public function __construct(UtilityContext $context, string $containerLocator)
    {
        parent::__construct($context);
        $this->fields = [
            'list' => $containerLocator,
            'tableCell' => $containerLocator . ' tr:nth-child(%d) td:nth-child(%d)',
            'editButton' => $containerLocator . ' tr:nth-child(%s) a[title=Edit]',
            'listRow' => $containerLocator . ' tbody tr',
        ];
    }

    abstract public function getTableCellValue(string $header, ?string $secondHeader = null): string;

    public function getItemCount(): int
    {
        return count($this->context->getSession()->getPage()->findAll('css', $this->fields['listRow']));
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

    protected function selectElement(string $name, string $selector): void
    {
        $position = $this->context->getElementPositionByText($name, $selector);
        $this->context->findElement(sprintf($this->fields['tableCell'], $position, 1) . $this->fields['checkboxInput'])->check();
    }

    protected function clickEditButtonByElementLocator(string $listItemName, string $locator): void
    {
        $position = $this->context->getElementPositionByText($listItemName, $locator);
        $this->context->findElement(sprintf($this->fields['editButton'], $position))->click();
    }
}
