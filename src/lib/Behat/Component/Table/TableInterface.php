<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\AdminUi\Behat\Component\Table;

interface TableInterface
{
    public function isEmpty(): bool;

    public function hasElement(array $elementData): bool;

    public function hasElementOnCurrentPage(array $elementData): bool;

    public function getTableRow(array $elementData): TableRow;

    public function getTableRowByIndex(int $rowIndex): TableRow;
}
