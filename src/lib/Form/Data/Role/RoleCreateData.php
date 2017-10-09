<?php
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Data\Role;

class RoleCreateData
{
    /** @var string */
    private $identifier;

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