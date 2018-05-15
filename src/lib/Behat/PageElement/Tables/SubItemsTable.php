<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\PageElement\Tables;

use Behat\Mink\Element\NodeElement;
use EzSystems\EzPlatformAdminUi\Behat\Helper\UtilityContext;
use PHPUnit\Framework\Assert;

class SubItemsTable extends Table
{
    public const ELEMENT_NAME = 'Sub-items Table';

    public function __construct(UtilityContext $context, $containerLocator)
    {
        parent::__construct($context, $containerLocator);
        $this->fields['horizontalHeaders'] = $this->fields['list'] . ' .c-table-view__cell--head';
        $this->fields['listElementLink'] = $this->fields['list'] . ' .c-table-view-item__link';
        $this->fields['editButton'] = $this->fields['list'] . ' .c-table-view-item__btn--edit';
        $this->fields['noItems'] = $this->fields['list'] . ' .c-no-items';
    }

    public function getTableCellValue(string $header, ?string $secondHeader = null): string
    {
        $columnPosition = $this->context->getElementPositionByText(
            $header,
            $this->fields['horizontalHeaders']
        );
        $rowPosition = $this->context->getElementPositionByText(
            $secondHeader,
            $this->fields['listElementLink']
        );

        return $this->getCellValue($rowPosition, $columnPosition);
    }

    /**
     * Click link element for sub-item with given name.
     *
     * @param string $name
     */
    public function clickListElement(string $name): void
    {
        Assert::assertTrue($this->isElementInTable($name), sprintf('There\'s no subitem %s on Sub-item list', $name));
        $this->context->getElementByText($name, $this->fields['listElementLink'])->click();
    }

    public function clickEditButton(string $listItemName): void
    {
        $this->clickEditButtonByElementLocator($listItemName, $this->fields['listElementLink']);
    }

    /**
     * Check if list contains link element with given name.
     *
     * @param string $name
     *
     * @return bool
     */
    public function isElementInTable(string $name, ?string $contentType = null): bool
    {
        if ($this->context->checkVisibilityByClass($this->fields['noItems'])) {
            return false;
        } else {
            $tableHash = $this->getTableHash();
            foreach ($tableHash as $row) {
                if (($row['Name'] === $name) && (($row['Content type'] === $contentType) || ($contentType === null))) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @return array all table records as hash map
     */
    public function getTableHash(): array
    {
        $tableHash = [];

        /** @var NodeElement[] $allHeaders */
        $allHeaders = $this->context->findAllWithWait($this->fields['horizontalHeaders']);
        /** @var NodeElement[] $allRows */
        $allRows = $this->context->findAllWithWait($this->fields['listRow']);
        $j = 0;
        foreach ($allRows as $row) {
            $rowHash = [];
            /** @var NodeElement[] $allCells */
            $allCells = $row->findAll('css', 'td');
            $headersCount = count($allHeaders);
            for ($i = 0; $i < $headersCount; ++$i) {
                $rowHash[$allHeaders[$i]->getText()] = $allCells[$i]->getText();
            }
            $tableHash[$j] = $rowHash;
            ++$j;
        }

        return $tableHash;
    }
}
