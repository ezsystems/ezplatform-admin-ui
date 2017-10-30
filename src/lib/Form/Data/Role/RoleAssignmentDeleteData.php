<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Data\Role;

use eZ\Publish\API\Repository\Values\User\RoleAssignment;

class RoleAssignmentDeleteData
{
    /** @var RoleAssignment */
    private $roleAssignment;

    public function __construct(?RoleAssignment $roleAssignment = null)
    {
        $this->roleAssignment = $roleAssignment;
    }

    /**
     * @return RoleAssignment
     */
    public function getRoleAssignment(): ?RoleAssignment
    {
        return $this->roleAssignment;
    }

    /**
     * @param RoleAssignment $roleAssignment
     */
    public function setRoleAssignment(RoleAssignment $roleAssignment)
    {
        $this->roleAssignment = $roleAssignment;
    }
}
