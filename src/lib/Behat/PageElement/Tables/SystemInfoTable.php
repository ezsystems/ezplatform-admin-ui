<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\PageElement\Tables;

use EzSystems\EzPlatformAdminUi\Behat\Helper\UtilityContext;
use PHPUnit\Framework\Assert;

class SystemInfoTable extends Table
{
    public const ELEMENT_NAME = 'System Info Table';

    public function __construct(UtilityContext $context, $containerLocator)
    {
        parent::__construct($context, $containerLocator);
        $this->fields['horizontalHeaders'] = $this->fields['list'] . ' thead th';
        $this->fields['listElement'] = $this->fields['list'] . ' td:nth-child(1)';
    }

    public function getTableCellValue(string $header, ?string $secondHeader = null): string
    {
        $columnPosition = $this->context->getElementPositionByText(
            $header,
            $this->fields['listElement']
        );

        return $this->getCellValue($columnPosition, 2);
    }

    public function verifyHeader(string $header): void
    {
        Assert::assertEquals(
            $header,
            $this->context->findElement($this->fields['horizontalHeaders'])->getText(),
            'System info table "%s" has wrong header.'
        );
    }
}
