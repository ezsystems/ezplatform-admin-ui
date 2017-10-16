<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\DataTransformer;

use eZ\Publish\API\Repository\RoleService;
use Symfony\Component\Form\DataTransformerInterface;
use eZ\Publish\Core\Repository\Values\User\UserRoleAssignment as RoleAssignment;
use Symfony\Component\Form\Exception\TransformationFailedException;
use eZ\Publish\API\Repository\Exceptions\NotFoundException;

/**
 * Transforms between a RoleAssignment's ID and a domain specific object.
 */
class RoleAssignmentTransformer implements DataTransformerInterface
{
    /** @var RoleService */
    protected $roleService;

    /**
     * @param RoleService $roleService
     */
    public function __construct(RoleService $roleService)
    {
        $this->roleService = $roleService;
    }

    /**
     * Transforms a domain specific RoleAssignment object into a RoleAssignment string.
     * @param mixed $value
     * @return mixed|null
     */
    public function transform($value)
    {
        if (null === $value) {
            return null;
        }

        if (!$value instanceof RoleAssignment) {
            throw new TransformationFailedException('Expected a ' . RoleAssignment::class . ' object.');
        }

        return $value->id;
    }

    /**
     * Transforms a RoleAssignment's ID into a domain specific RoleAssignment object.
     * @param mixed $value
     * @return \eZ\Publish\API\Repository\Values\User\RoleAssignment|null
     */
    public function reverseTransform($value)
    {
        if (empty($value)) {
            return null;
        }

        try {
            return $this->roleService->loadRoleAssignment($value);
        } catch (NotFoundException $e) {
            throw new TransformationFailedException('Transformation failed. ' . $e->getMessage(), $e->getCode(), $e);
        }
    }
}
