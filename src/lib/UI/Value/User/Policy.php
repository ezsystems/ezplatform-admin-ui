<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\UI\Value\User;

use eZ\Publish\API\Repository\Values\User\Policy as  APIPolicy;

class Policy extends APIPolicy
{
    /**
     * Limitations assigned to this policy.
     *
     * @var \eZ\Publish\API\Repository\Values\User\Limitation[]
     */
    protected $limitations = [];

    /**
     * RoleAssignment to which policy belongs.
     *
     * @var \eZ\Publish\API\Repository\Values\User\RoleAssignment
     */
    protected $role_assignment;

    /**
     * @param \eZ\Publish\API\Repository\Values\User\Policy $policy
     * @param array $properties
     */
    public function __construct(APIPolicy $policy, array $properties = [])
    {
        parent::__construct(get_object_vars($policy) + $properties);

        $this->limitations = $policy->getLimitations();
    }

    /**
     * @return \eZ\Publish\API\Repository\Values\User\Limitation[]
     */
    public function getLimitations(): iterable
    {
        return $this->limitations;
    }
}
