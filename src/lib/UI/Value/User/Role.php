<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\UI\Value\User;

use eZ\Publish\API\Repository\Values\User\Limitation\RoleLimitation as APIRoleLimitation;
use eZ\Publish\API\Repository\Values\User\Role as APIRole;
use eZ\Publish\API\Repository\Values\User\RoleAssignment;

class Role extends RoleAssignment
{
    /**
     * the limitation of this role assignment.
     *
     * @var \eZ\Publish\API\Repository\Values\User\Limitation\RoleLimitation|null
     */
    protected $limitation;

    /**
     * the role which is assigned to the user.
     *
     * @var \eZ\Publish\API\Repository\Values\User\Role
     */
    protected $role;

    /**
     * Returns the limitation of the user role assignment.
     *
     * @return \eZ\Publish\API\Repository\Values\User\Limitation\RoleLimitation|null
     */
    public function getRoleLimitation(): ?APIRoleLimitation
    {
        return $this->limitation;
    }

    /**
     * Returns the role to which the user is assigned to.
     *
     * @return \eZ\Publish\API\Repository\Values\User\Role
     */
    public function getRole(): APIRole
    {
        return $this->role;
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\User\RoleAssignment $roleAssignment
     * @param array $properties
     */
    public function __construct(RoleAssignment $roleAssignment, array $properties = [])
    {
        parent::__construct(get_object_vars($roleAssignment) + $properties);

        $this->role = $roleAssignment->role;
        $this->limitation = $roleAssignment->limitation;
    }
}
