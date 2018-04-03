<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\PageElement;

use EzSystems\EzPlatformAdminUi\Behat\Helper\UtilityContext;

class SimpleTable extends Table
{
    /** @var string Name by which Element is recognised */
    public const ELEMENT_NAME = 'Simple Table';

    public function __construct(UtilityContext $context, $containerLocator)
    {
        parent::__construct($context, $containerLocator);
        $this->fields['horizontalHeaders'] = $this->fields['list'] . ' .ez-table-header + .table thead th, .ez-table-header + form thead th';
        $this->fields['listElement'] = $this->fields['list'] . ' td:nth-child(1)';
    }

    public function getTableCellValue(string $header, ?string $secondHeader = null): string
    {
        $columnPosition = $this->context->getElementPositionByText(
            $header,
            $this->fields['horizontalHeaders']
        );

        $rowPosition = $secondHeader ?
            $this->context->getElementPositionByText($secondHeader, $this->fields['listElement'])
            : 1;

        return $this->getCellValue($rowPosition, $columnPosition);
    }

    public function clickEditButton(string $listItemName): void
    {
        $this->clickEditButtonByElementLocator($listItemName, $this->fields['listElement']);
    }
}
