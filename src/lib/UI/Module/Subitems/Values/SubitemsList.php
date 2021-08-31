<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\UI\Module\Subitems\Values;

use EzSystems\EzPlatformRest\Value as RestValue;

class SubitemsList extends RestValue
{
    /** @var SubitemsRow[] */
    public $subitemRows;

    /** @var int */
    public $childrenCount;

    /**
     * @param SubitemsRow[] $subitemRows
     * @param int $childrenCount
     */
    public function __construct(array $subitemRows, int $childrenCount)
    {
        $this->subitemRows = $subitemRows;
        $this->childrenCount = $childrenCount;
    }
}
