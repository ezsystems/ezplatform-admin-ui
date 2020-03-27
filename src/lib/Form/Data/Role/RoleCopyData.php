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
    /** @var eZ\Publish\API\Repository\Values\User\Role */
    private $copiedRole;

    /** @var string */
    private $newIdentifier;

    /**
     * @param Role $role
     * @param string|null $identifier
     */
    public function __construct(Role $role, ?string $identifier = null)
    {
        $this->copiedRole = $role;
        $this->newIdentifier = $identifier;
    }

    /**
     * @return Role
     */
    public function getCopiedRole(): ?Role
    {
        return $this->copiedRole;
    }

    /**
     * @param Role $role
     *
     * @return RoleCopyData
     */
    public function setCopiedRole(Role $role): self
    {
        $this->copiedRole = $role;

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
