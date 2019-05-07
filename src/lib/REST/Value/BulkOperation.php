<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\REST\Value;

use EzSystems\EzPlatformRestCommon\Value as RestValue;

class BulkOperation extends RestValue
{
    /** @var \EzSystems\EzPlatformAdminUi\REST\Value\Operation[] */
    public $operations;

    /**
     * @param \EzSystems\EzPlatformAdminUi\REST\Value\Operation[] $operations
     */
    public function __construct(array $operations)
    {
        $this->operations = $operations;
    }
}
