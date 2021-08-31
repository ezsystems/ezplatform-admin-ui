<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Form\Data;

class RoleAssignmentData
{
    /** @var \eZ\Publish\API\Repository\Values\User\UserGroup[] */
    private $groups;

    /** @var \eZ\Publish\API\Repository\Values\User\User[] */
    private $users;

    /** @var \eZ\Publish\API\Repository\Values\Content\Section[] */
    private $sections;

    /** @var \eZ\Publish\API\Repository\Values\Content\Location[] */
    private $locations;

    /**
     * @return \eZ\Publish\API\Repository\Values\User\UserGroup[]
     */
    public function getGroups(): array
    {
        return $this->groups;
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\User\UserGroup[] $groups
     */
    public function setGroups(array $groups)
    {
        $this->groups = $groups;
    }

    /**
     * @return \eZ\Publish\API\Repository\Values\User\User[]
     */
    public function getUsers(): array
    {
        return $this->users;
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\User\User[] $users
     */
    public function setUsers(array $users)
    {
        $this->users = $users;
    }

    /**
     * @return \eZ\Publish\API\Repository\Values\Content\Section[]
     */
    public function getSections(): array
    {
        return $this->sections;
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Section[] $sections
     */
    public function setSections(array $sections)
    {
        $this->sections = $sections;
    }

    /**
     * @return \eZ\Publish\API\Repository\Values\Content\Location[]
     */
    public function getLocations(): array
    {
        return $this->locations;
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Location[] $locations
     */
    public function setLocations(array $locations)
    {
        $this->locations = $locations;
    }
}
