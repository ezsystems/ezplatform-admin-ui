<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\AdminUi\Behat\Component\Table;

use eZ\Publish\API\Repository\Exceptions\NotImplementedException;
use Ibexa\Behat\Browser\Component\Component;
use Ibexa\Behat\Browser\Element\Criterion\ElementTextCriterion;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;

class SubitemsGrid extends Component implements TableInterface
{
    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('listElement', '.m-sub-items .ibexa-grid-view-item'),
            new VisibleCSSLocator('parent', '.m-sub-items'),
        ];
    }

    public function verifyIsLoaded(): void
    {
    }

    public function isEmpty(): bool
    {
        return $this->getHTMLPage()->findAll($this->getLocator('listElement'))->empty();
    }

    public function hasElement(array $elementData): bool
    {
        return $this->getHTMLPage()
            ->findAll($this->getLocator('listElement'))
            ->filterBy(new ElementTextCriterion($elementData['Name']))
            ->any();
    }

    public function hasElementOnCurrentPage(array $elementData): bool
    {
        return $this->getHTMLPage()
            ->findAll($this->getLocator('listElement'))
            ->filterBy(new ElementTextCriterion($elementData['Name']))
            ->any();
    }

    public function getTableRow(array $elementData): TableRow
    {
        throw new NotImplementedException('Getting Table row in SubitemsGrid');
    }

    public function getTableRowByIndex(int $rowIndex): TableRow
    {
        throw new NotImplementedException('Getting by row index in SubitemsGrid');
    }
}
