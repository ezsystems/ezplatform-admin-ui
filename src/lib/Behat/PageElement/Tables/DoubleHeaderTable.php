<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\PageElement\Tables;

use EzSystems\Behat\Browser\Context\BrowserContext;

class DoubleHeaderTable extends Table
{
    /** @var string Name by which Element is recognised */
    public const ELEMENT_NAME = 'Double Header Table';

    public function __construct(BrowserContext $context, $containerLocator)
    {
        parent::__construct($context, $containerLocator);
        $this->fields['horizontalHeaders'] = $this->fields['list'] . ' .ez-table__header + .table thead th';
        $this->fields['insideHeaders'] = $this->fields['list'] . ' thead+ tbody th';
    }

    public function getTableCellValue(string $header, ?string $secondHeader = null): string
    {
        $columnPosition = $this->context->getElementPositionByText(
            $header,
            $this->fields['horizontalHeaders']
        );
        $rowPosition = $this->context->getElementPositionByText(
            $secondHeader,
            $this->fields['insideHeaders']
        );

        return $this->getCellValue($rowPosition, $columnPosition);
    }
}
