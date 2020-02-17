<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Data\Role;

use eZ\Publish\API\Repository\Values\User\Role;

class RoleCopyData
{
    /** @var Role */
    private $clonedRole;

    /** @var string */
    private $newIdentifier;

    /**
     * @param Role $role
     * @param string|null $identifier
     */
    public function __construct(Role $role, ?string $identifier = null)
    {
        $this->clonedRole = $role;
        $this->newIdentifier = $identifier;
    }

    /**
     * @return Role
     */
    public function getClonedRole(): ?Role
    {
        return $this->clonedRole;
    }

    /**
     * @param Role $role
     *
     * @return RoleCopyData
     */
    public function setClonedRole(Role $role): self
    {
        $this->clonedRole = $role;

        return $this;
    }

    /**
     * @return string
     */
    public function getNewIdentifier(): ?string
    {
        return $this->newIdentifier;
    }

    /**
     * @param string|null $identifier
     *
     * @return RoleCopyData
     */
    public function setNewIdentifier(?string $identifier): self
    {
        $this->newIdentifier = $identifier;

        return $this;
    }
}
