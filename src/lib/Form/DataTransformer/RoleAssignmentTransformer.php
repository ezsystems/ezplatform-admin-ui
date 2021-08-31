<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\DataTransformer;

use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\RoleService;
use eZ\Publish\API\Repository\Values\User\RoleAssignment as APIRoleAssignment;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * Transforms between a Role Assignment's identifier and a domain specific object.
 */
class RoleAssignmentTransformer implements DataTransformerInterface
{
    /** @var \eZ\Publish\API\Repository\RoleService */
    protected $roleService;

    /**
     * @param \eZ\Publish\API\Repository\RoleService $roleService
     */
    public function __construct(RoleService $roleService)
    {
        $this->roleService = $roleService;
    }

    /**
     * Transforms a domain specific RoleAssignment object into a RoleAssignment string.
     *
     * @param mixed $value
     *
     * @return mixed|null
     *
     * @throws \Symfony\Component\Form\Exception\TransformationFailedException
     */
    public function transform($value)
    {
        if (null === $value) {
            return null;
        }

        if (!$value instanceof APIRoleAssignment) {
            throw new TransformationFailedException('Expected a ' . APIRoleAssignment::class . ' object.');
        }

        return $value->id;
    }

    /**
     * Transforms a RoleAssignment's ID into a domain specific RoleAssignment object.
     *
     * @param mixed $value
     *
     * @return \eZ\Publish\API\Repository\Values\User\RoleAssignment|null
     *
     * @throws \Symfony\Component\Form\Exception\TransformationFailedException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
    public function reverseTransform($value): ?APIRoleAssignment
    {
        if (empty($value)) {
            return null;
        }

        if (!ctype_digit($value)) {
            throw new TransformationFailedException('Expected a numeric string.');
        }

        try {
            return $this->roleService->loadRoleAssignment((int)$value);
        } catch (NotFoundException $e) {
            throw new TransformationFailedException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
