<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\PageElement\Tables;

use EzSystems\Behat\Browser\Context\BrowserContext;

class VerticalOrientedTable extends Table
{
    /** @var string Name by which Element is recognised */
    public const ELEMENT_NAME = 'Vertical Oriented Table';

    public function __construct(BrowserContext $context, $containerLocator)
    {
        parent::__construct($context, $containerLocator);
        $this->fields['verticalHeaders'] = $this->fields['list'] . ' colgroup+ tbody th';
    }

    public function getTableCellValue(string $header, ?string $secondHeader = null): string
    {
        $rowPosition = $this->context->getElementPositionByText(
            $header,
            $this->fields['verticalHeaders']
        );

        return $this->getCellValue($rowPosition, 2);
    }
}
