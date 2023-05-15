<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\AdminUi\Pagination\Pagerfanta;

use eZ\Publish\API\Repository\RoleService;
use eZ\Publish\API\Repository\Values\User\Role;
use Pagerfanta\Adapter\AdapterInterface;

final class RoleAssignmentsSearchAdapter implements AdapterInterface
{
    /** @var \eZ\Publish\API\Repository\RoleService */
    private $roleService;

    /** @var \eZ\Publish\API\Repository\Values\User\Role */
    private $role;

    /** @var int|null */
    private $assignmentsCount;

    public function __construct(RoleService $roleService, Role $role, ?int $assignmentsCount = null)
    {
        $this->roleService = $roleService;
        $this->role = $role;
        $this->assignmentsCount = $assignmentsCount;
    }

    /**
     * @throws \eZ\Publish\API\Repository\Exceptions\BadStateException
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
    public function getNbResults(): int
    {
        return $this->assignmentsCount ?: $this->roleService->countRoleAssignments($this->role);
    }

    /**
     * @throws \eZ\Publish\API\Repository\Exceptions\BadStateException
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
    public function getSlice($offset, $length): iterable
    {
        return $this->roleService->loadRoleAssignments($this->role, $offset, $length);
    }
}
