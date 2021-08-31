<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Data\Policy;

use eZ\Publish\API\Repository\Values\User\Role;

/**
 * @todo Add validation
 */
class PoliciesDeleteData
{
    /** @var \eZ\Publish\API\Repository\Values\User\Role|null */
    protected $role;

    /** @var array|null */
    protected $policies;

    /**
     * @param \eZ\Publish\API\Repository\Values\User\Role|null $role
     * @param array|null $policies
     */
    public function __construct(?Role $role = null, array $policies = [])
    {
        $this->role = $role;
        $this->policies = $policies;
    }

    /**
     * @return \eZ\Publish\API\Repository\Values\User\Role|null
     */
    public function getRole(): ?Role
    {
        return $this->role;
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\User\Role|null $role
     */
    public function setRole(?Role $role)
    {
        $this->role = $role;
    }

    /**
     * @return array|null
     */
    public function getPolicies(): ?array
    {
        return $this->policies;
    }

    /**
     * @param array|null $policies
     */
    public function setPolicies(?array $policies)
    {
        $this->policies = $policies;
    }
}
