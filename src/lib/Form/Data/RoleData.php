<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\AdminUi\Form\Data;

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

    public function getIdentifier(): ?string
    {
        return $this->identifier;
    }

    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;
    }

    public static function factory(Role $role): self
    {
        return new self($role->identifier);
    }
}

class_alias(RoleData::class, 'EzSystems\EzPlatformAdminUi\Form\Data\RoleData');
