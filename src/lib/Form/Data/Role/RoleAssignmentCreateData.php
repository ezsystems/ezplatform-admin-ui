<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Data\Role;

use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\API\Repository\Values\Content\Section;
use eZ\Publish\API\Repository\Values\User\User;
use eZ\Publish\API\Repository\Values\User\UserGroup;
use EzSystems\EzPlatformAdminUi\Form\Data\OnFailureRedirect;
use EzSystems\EzPlatformAdminUi\Form\Data\OnFailureRedirectTrait;
use EzSystems\EzPlatformAdminUi\Form\Data\OnSuccessRedirect;
use EzSystems\EzPlatformAdminUi\Form\Data\OnSuccessRedirectTrait;

class RoleAssignmentCreateData implements OnSuccessRedirect, OnFailureRedirect
{
    use OnSuccessRedirectTrait;
    use OnFailureRedirectTrait;

    /** @var UserGroup[] */
    private $groups;

    /** @var User[] */
    private $users;

    /** @var Section[] */
    private $sections;

    /** @var Location[] */
    private $locations;

    /**
     * @return UserGroup[]
     */
    public function getGroups(): ?array
    {
        return $this->groups;
    }

    /**
     * @param UserGroup[] $groups
     */
    public function setGroups(array $groups)
    {
        $this->groups = $groups;
    }

    /**
     * @return User[]
     */
    public function getUsers(): ?array
    {
        return $this->users;
    }

    /**
     * @param User[] $users
     */
    public function setUsers(array $users)
    {
        $this->users = $users;
    }

    /**
     * @return Section[]
     */
    public function getSections(): ?array
    {
        return $this->sections;
    }

    /**
     * @param Section[] $sections
     */
    public function setSections(array $sections)
    {
        $this->sections = $sections;
    }

    /**
     * @return Location[]
     */
    public function getLocations(): ?array
    {
        return $this->locations;
    }

    /**
     * @param Location[] $locations
     */
    public function setLocations(array $locations)
    {
        $this->locations = $locations;
    }
}
