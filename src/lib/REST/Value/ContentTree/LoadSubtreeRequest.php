<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\REST\Value\ContentTree;

use EzSystems\EzPlatformRest\Value as RestValue;

class LoadSubtreeRequest extends RestValue
{
    /** @var \EzSystems\EzPlatformAdminUi\REST\Value\ContentTree\LoadSubtreeRequestNode[] */
    public $nodes;

    /**
     * @param array $nodes
     */
    public function __construct(array $nodes = [])
    {
        $this->nodes = $nodes;
    }
}
