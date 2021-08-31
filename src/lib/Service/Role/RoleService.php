<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Service\Role;

use eZ\Publish\API\Repository;
use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\ContentId;
use eZ\Publish\API\Repository\Values\User\Limitation\RoleLimitation;
use eZ\Publish\API\Repository\Values\User\Limitation\SectionLimitation;
use eZ\Publish\API\Repository\Values\User\Limitation\SubtreeLimitation;
use eZ\Publish\API\Repository\Values\User\Policy;
use eZ\Publish\API\Repository\Values\User\Role;
use eZ\Publish\API\Repository\Values\User\RoleAssignment;
use EzSystems\EzPlatformAdminUi\Form\Data\PolicyData;
use EzSystems\EzPlatformAdminUi\Form\Data\RoleAssignmentData;
use EzSystems\EzPlatformAdminUi\Form\Data\RoleData;

class RoleService
{
    /** @var Repository\RoleService */
    private $roleService;

    /** @var Repository\SearchService */
    private $searchService;

    /**
     * RoleService constructor.
     *
     * @param Repository\RoleService $roleService
     * @param Repository\SearchService $searchService
     */
    public function __construct(Repository\RoleService $roleService, Repository\SearchService $searchService)
    {
        $this->roleService = $roleService;
        $this->searchService = $searchService;
    }

    public function getRole(int $id): Role
    {
        return $this->roleService->loadRole($id);
    }

    public function getRoles()
    {
        return $this->roleService->loadRoles();
    }

    public function createRole(RoleData $data): Role
    {
        $roleCreateStruct = $this->roleService->newRoleCreateStruct(
            $data->getIdentifier()
        );

        $role = $this->roleService->createRole($roleCreateStruct);
        $this->roleService->publishRoleDraft($role);

        return $role;
    }

    public function updateRole(Role $role, RoleData $data): Role
    {
        $roleUpdateStruct = $this->roleService->newRoleUpdateStruct();
        $roleUpdateStruct->identifier = $data->getIdentifier();

        $draft = $this->roleService->createRoleDraft($role);
        $this->roleService->updateRoleDraft($draft, $roleUpdateStruct);
        $this->roleService->publishRoleDraft($draft);

        return $draft;
    }

    public function deleteRole(Role $role)
    {
        $this->roleService->deleteRole($role);
    }

    public function getPolicy(Role $role, int $policyId)
    {
        foreach ($role->getPolicies() as $policy) {
            if ($policy->id === $policyId) {
                return $policy;
            }
        }

        return null;
    }

    public function createPolicy(Role $role, PolicyData $data): Role
    {
        $policyCreateStruct = $this->roleService->newPolicyCreateStruct(
            $data->getModule(),
            $data->getFunction()
        );

        $draft = $this->roleService->createRoleDraft($role);
        $this->roleService->addPolicyByRoleDraft($draft, $policyCreateStruct);
        $this->roleService->publishRoleDraft($draft);

        return $draft;
    }

    public function deletePolicy(Role $role, Policy $policy)
    {
        $draft = $this->roleService->createRoleDraft($role);
        foreach ($draft->getPolicies() as $policyDraft) {
            if ($policyDraft->originalId == $policy->id) {
                $this->roleService->removePolicyByRoleDraft($draft, $policyDraft);
                $this->roleService->publishRoleDraft($draft);

                return;
            }
        }

        throw new \RuntimeException("Policy {$policy->id} not found.");
    }

    public function updatePolicy(Role $role, Policy $policy, PolicyData $data): Role
    {
        $policyUpdateStruct = $this->roleService->newPolicyUpdateStruct();
        foreach ($data->getLimitations() as $limitation) {
            if (!empty($limitation->limitationValues)) {
                $policyUpdateStruct->addLimitation($limitation);
            }
        }

        $roleDraft = $this->roleService->createRoleDraft($role);
        foreach ($roleDraft->getPolicies() as $policyDraft) {
            if ($policyDraft->originalId == $policy->id) {
                $this->roleService->updatePolicyByRoleDraft($roleDraft, $policyDraft, $policyUpdateStruct);
                $this->roleService->publishRoleDraft($roleDraft);

                return $roleDraft;
            }
        }

        throw new \RuntimeException("Policy {$policy->id} not found.");
    }

    public function getRoleAssignments(Role $role)
    {
        return $this->roleService->getRoleAssignments($role);
    }

    public function getRoleAssignment(int $roleAssignmentId): RoleAssignment
    {
        return $this->roleService->loadRoleAssignment($roleAssignmentId);
    }

    public function removeRoleAssignment(RoleAssignment $roleAssignment)
    {
        $this->roleService->removeRoleAssignment($roleAssignment);
    }

    public function assignRole(Role $role, RoleAssignmentData $data)
    {
        $users = $data->getUsers();
        $groups = $data->getGroups();

        $sections = $data->getSections();
        $locations = $data->getLocations();

        if (empty($sections) && empty($locations)) {
            // Assign role to user/groups without limitations
            $this->doAssignLimitation($role, $users, $groups);

            return;
        }

        if (!empty($sections)) {
            $limitation = new SectionLimitation();
            $limitation->limitationValues = [];
            foreach ($sections as $section) {
                $limitation->limitationValues[] = $section->id;
            }

            // Assign role to user/groups with section limitations
            $this->doAssignLimitation($role, $users, $groups, $limitation);
        }

        if (!empty($locations)) {
            $limitation = new SubtreeLimitation();
            $limitation->limitationValues = [];

            $query = new LocationQuery();
            $query->filter = new ContentId($locations);

            $result = $this->searchService->findLocations($query);
            foreach ($result->searchHits as $searchHit) {
                /** @var Repository\Values\Content\Location $location */
                $limitation->limitationValues[] = $searchHit->valueObject->pathString;
            }

            // Assign role to user/groups with subtree limitations
            $this->doAssignLimitation($role, $users, $groups, $limitation);
        }
    }

    private function doAssignLimitation(Role $role, array $users = null, array $groups = null, RoleLimitation $limitation = null)
    {
        if (null !== $users) {
            foreach ($users as $user) {
                $this->roleService->assignRoleToUser($role, $user, $limitation);
            }
        }

        if (null !== $groups) {
            foreach ($groups as $group) {
                $this->roleService->assignRoleToUserGroup($role, $group, $limitation);
            }
        }
    }
}
