<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\REST\Value;

use eZ\Publish\Core\REST\Common\Value as RestValue;

class BulkOperationResponse extends RestValue
{
    /** @var \EzSystems\EzPlatformAdminUi\REST\Value\OperationResponse[] */
    public $operations;

    /**
     * @param \EzSystems\EzPlatformAdminUi\REST\Value\OperationResponse[] $operations
     */
    public function __construct($operations)
    {
        $this->operations = $operations;
    }
}
