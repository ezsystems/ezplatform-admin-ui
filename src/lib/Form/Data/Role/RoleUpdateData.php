<?php
declare(strict_types=1);

namespace EzPlatformAdminUi\Form\Data\Role;

use eZ\Publish\API\Repository\Values\User\Role;

class RoleUpdateData
{
    /** @var Role */
    private $role;

    /** @var string */
    private $identifier;

    /**
     * @param Role|null $role
     */
    public function __construct(?Role $role = null)
    {
        $this->role = $role;
        $this->identifier = $role->identifier;
    }

    /**
     * @return Role
     */
    public function getRole(): ?Role
    {
        return $this->role;
    }

    /**
     * @param Role $role
     */
    public function setRole(Role $role)
    {
        $this->role = $role;
    }

    /**
     * @return string
     */
    public function getIdentifier(): ?string
    {
        return $this->identifier;
    }

    /**
     * @param string $identifier
     */
    public function setIdentifier(string $identifier)
    {
        $this->identifier = $identifier;
    }
}