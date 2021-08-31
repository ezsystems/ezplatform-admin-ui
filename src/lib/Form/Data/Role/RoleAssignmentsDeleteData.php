<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Data\Role;

use eZ\Publish\API\Repository\Values\User\Role;

/**
 * @todo Add validation
 */
class RoleAssignmentsDeleteData
{
    /** @var \eZ\Publish\API\Repository\Values\User\Role|null */
    protected $role;

    /** @var array|null */
    protected $roleAssignments;

    /**
     * @param \eZ\Publish\API\Repository\Values\User\Role|null $role
     * @param array|null $roleAssignments
     */
    public function __construct(?Role $role = null, array $roleAssignments = [])
    {
        $this->role = $role;
        $this->roleAssignments = $roleAssignments;
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
    public function getRoleAssignments(): ?array
    {
        return $this->roleAssignments;
    }

    /**
     * @param array|null $roleAssignments
     */
    public function setRoleAssignments(?array $roleAssignments)
    {
        $this->roleAssignments = $roleAssignments;
    }
}
