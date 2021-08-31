<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Tab;

/**
 * Conditional Tab interface needs to be implemented by tabs,
 * which needs to be evaluate depends on context.
 */
interface ConditionalTabInterface
{
    /**
     * Get information about tab presence.
     *
     * @param array $parameters
     *
     * @return bool
     */
    public function evaluate(array $parameters): bool;
}
