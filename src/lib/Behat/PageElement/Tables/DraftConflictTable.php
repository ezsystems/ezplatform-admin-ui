<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\PageElement\Tables;

use EzSystems\Behat\Browser\Context\BrowserContext;

class DraftConflictTable extends Table
{
    /** @var string Name by which Element is recognised */
    public const ELEMENT_NAME = 'Draft conflict Table';

    public function __construct(BrowserContext $context, string $containerLocator)
    {
        parent::__construct($context, $containerLocator);
        $this->fields['listElement'] = $this->fields['list'] . ' tbody td:nth-child(1)';
        $this->fields['editButton'] = $this->fields['list'] . ' tr:nth-child(%s) .ez-icon-edit';
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

    public function clickEditButton(string $listItemName): void
    {
        $this->clickEditButtonByElementLocator($listItemName, $this->fields['listElement']);
    }
}
