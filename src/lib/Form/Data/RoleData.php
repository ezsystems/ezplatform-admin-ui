<?php

declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Data;

use eZ\Publish\API\Repository\Values\User\Role;

class RoleData
{
    /** @var string */
    private $identifier;

    /**
     * RoleData constructor.
     *
     * @param string $identifier
     */
    public function __construct($identifier = null)
    {
        $this->identifier = $identifier;
    }

    public function getIdentifier()
    {
        return $this->identifier;
    }

    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;
    }

    public static function factory(Role $role): RoleData
    {
        return new RoleData($role->identifier);
    }
}
