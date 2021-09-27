<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\AdminUi\UI\Module\FieldTypeToolbar\Values;

use Iterator;
use IteratorAggregate;

final class FieldTypeToolbar implements IteratorAggregate
{
    /** @var \Ibexa\AdminUi\UI\Module\FieldTypeToolbar\Values\FieldTypeToolbarItem[] */
    private $items;

    public function __construct(array $fieldTypes)
    {
        $this->items = $fieldTypes;
    }

    /**
     * @return \Ibexa\AdminUi\UI\Module\FieldTypeToolbar\Values\FieldTypeToolbarItem[]
     */
    public function getItems(): iterable
    {
        return $this->items;
    }

    /**
     * @return \Ibexa\AdminUi\UI\Module\FieldTypeToolbar\Values\FieldTypeToolbarItem[]
     */
    public function getIterator(): Iterator
    {
        yield from $this->items;
    }
}
