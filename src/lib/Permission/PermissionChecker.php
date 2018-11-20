<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Permission;

use eZ\Publish\API\Repository\PermissionResolver;
use eZ\Publish\API\Repository\UserService;
use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\API\Repository\Values\User\Limitation\LocationLimitation;
use eZ\Publish\API\Repository\Values\User\Limitation\ParentContentTypeLimitation;
use eZ\Publish\API\Repository\Values\User\Limitation\ParentDepthLimitation;
use eZ\Publish\API\Repository\Values\User\Limitation\ParentOwnerLimitation;
use eZ\Publish\API\Repository\Values\User\Limitation\ParentUserGroupLimitation;
use eZ\Publish\API\Repository\Values\User\Limitation\SectionLimitation;
use eZ\Publish\API\Repository\Values\User\Limitation\SubtreeLimitation;
use eZ\Publish\API\Repository\Values\User\User;

class PermissionChecker implements PermissionCheckerInterface
{
    private const USER_GROUPS_LIMIT = 25;

    /** @var \eZ\Publish\API\Repository\PermissionResolver */
    private $permissionResolver;

    /** @var \eZ\Publish\API\Repository\UserService */
    private $userService;

    /** @var array */
    private $flattenArrayOfLimitations;

    /**
     * @param \eZ\Publish\API\Repository\PermissionResolver $permissionResolver
     * @param \eZ\Publish\API\Repository\UserService $userService
     */
    public function __construct(
        PermissionResolver $permissionResolver,
        UserService $userService
    ) {
        $this->permissionResolver = $permissionResolver;
        $this->userService = $userService;
    }

    /**
     * @param $hasAccess
     * @param string $class
     *
     * @return array
     */
    public function getRestrictions($hasAccess, string $class): array
    {
        $restrictions = [];
        $oneOfPoliciesHasNoLimitation = false;

        foreach ($this->flattenArrayOfLimitationsForCurrentUser($hasAccess) as $policy => $limitations) {
            $policyHasLimitation = false;
            foreach ($limitations as $limitation) {
                if ($limitation instanceof $class) {
                    $restrictions[] = $limitation->limitationValues;
                    $policyHasLimitation = true;
                }
            }
            if (false === $policyHasLimitation) {
                $oneOfPoliciesHasNoLimitation = true;
            }
        }

        if ($oneOfPoliciesHasNoLimitation) {
            return [];
        }

        return empty($restrictions) ? $restrictions : array_unique(array_merge(...$restrictions));
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Location $location
     * @param array|bool $hasAccess
     *
     * @return bool
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
    public function canCreateInLocation(Location $location, $hasAccess): bool
    {
        if (is_bool($hasAccess)) {
            return $hasAccess;
        }
        $restrictedLocations = $this->getRestrictions($hasAccess, LocationLimitation::class);
        $canCreateInLocation = empty($restrictedLocations)
            ? true
            : \in_array($location->id, array_map('intval', $restrictedLocations), true);

        if (false === $canCreateInLocation) {
            return false;
        }

        $restrictedParentContentTypes = $this->getRestrictions($hasAccess, ParentContentTypeLimitation::class);
        $canCreateInParentContentType = empty($restrictedParentContentTypes)
            ? true
            : \in_array($location->contentInfo->contentTypeId, array_map('intval', $restrictedParentContentTypes), true);

        if (false === $canCreateInParentContentType) {
            return false;
        }

        $restrictedParentDepths = $this->getRestrictions($hasAccess, ParentDepthLimitation::class);
        $canCreateInParentDepth = empty($restrictedParentDepths)
            ? true
            : \in_array($location->depth, array_map('intval', $restrictedParentDepths), true);

        if (false === $canCreateInParentDepth) {
            return false;
        }

        $restrictedParentOwner = $this->getRestrictions($hasAccess, ParentOwnerLimitation::class);
        $canCreateInParentOwner = empty($restrictedParentOwner)
            ? true
            : $location->contentInfo->ownerId === $this->permissionResolver->getCurrentUserReference()->getUserId();

        if (false === $canCreateInParentOwner) {
            return false;
        }

        $restrictedSections = $this->getRestrictions($hasAccess, SectionLimitation::class);
        $canCreateInSection = empty($restrictedSections)
            ? true
            : \in_array($location->contentInfo->sectionId, array_map('intval', $restrictedSections), true);

        if (false === $canCreateInSection) {
            return false;
        }

        $restrictedParentUserGroups = $this->getRestrictions($hasAccess, ParentUserGroupLimitation::class);
        $canCreateInParentUserGroup = empty($restrictedParentUserGroups)
            ? true
            : $this->hasSameParentUserGroup($location);

        if (false === $canCreateInParentUserGroup) {
            return false;
        }

        $restrictedSubtrees = $this->getRestrictions($hasAccess, SubtreeLimitation::class);
        $canCreateInSubtree = empty($restrictedSubtrees)
            ? true
            : !empty(array_filter($restrictedSubtrees, function ($restrictedSubtree) use ($location) {
                return strpos($location->pathString, $restrictedSubtree) === 0;
            }));

        if (false === $canCreateInSubtree) {
            return false;
        }

        return true;
    }

    /**
     * This method should only be used for very specific use cases. It should be used in a content cases
     * where assignment limitations are not relevant.
     *
     * @param array $hasAccess
     *
     * @return array
     */
    private function flattenArrayOfLimitationsForCurrentUser(array $hasAccess): array
    {
        $currentUserId = $this->permissionResolver->getCurrentUserReference()->getUserId();

        if (\is_array($this->flattenArrayOfLimitations[$currentUserId])) {
            return $this->flattenArrayOfLimitations[$currentUserId];
        }

        $limitations = [];
        foreach ($hasAccess as $permissionSet) {
            if ($permissionSet['limitation'] !== null) {
                $limitations[] = $permissionSet['limitation'];
            }
            /** @var \eZ\Publish\API\Repository\Values\User\Policy $policy */
            foreach ($permissionSet['policies'] as $policy) {
                $policyLimitations = $policy->getLimitations();
                if (!empty($policyLimitations)) {
                    foreach ($policyLimitations as $policyLimitation) {
                        $limitations[$policy->id][] = $policyLimitation;
                    }
                }
            }
        }

        return $this->flattenArrayOfLimitations[$currentUserId] = $limitations;
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Location $location
     *
     * @return bool
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
    private function hasSameParentUserGroup(Location $location): bool
    {
        $currentUserId = $this->permissionResolver->getCurrentUserReference()->getUserId();
        $currentUser = $this->userService->loadUser($currentUserId);
        $currentUserGroups = $this->loadAllUserGroupsIdsOfUser($currentUser);

        $locationOwnerId = $location->contentInfo->ownerId;
        $locationOwner = $this->userService->loadUser($locationOwnerId);
        $locationOwnerGroups = $this->loadAllUserGroupsIdsOfUser($locationOwner);

        return !empty(array_intersect($currentUserGroups, $locationOwnerGroups));
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\User\User $user
     *
     * @return int[]
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
    private function loadAllUserGroupsIdsOfUser(User $user): array
    {
        $allUserGroups = [];
        $offset = 0;

        do {
            $userGroups = $this->userService->loadUserGroupsOfUser($user, $offset, self::USER_GROUPS_LIMIT);
            foreach ($userGroups as $userGroup) {
                $allUserGroups[] = $userGroup->contentInfo->id;
            }
            $offset += self::USER_GROUPS_LIMIT;
        } while (\count($userGroups) === self::USER_GROUPS_LIMIT);

        return $allUserGroups;
    }
}
