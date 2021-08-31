<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Specification;

interface UserSpecification
{
    /**
     * Check to see if the specification is satisfied.
     *
     * @param mixed $userId
     *
     * @return bool
     */
    public function isSatisfiedBy($userId): bool;
}
