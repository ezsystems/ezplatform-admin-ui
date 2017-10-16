<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\DataTransformer;

use eZ\Publish\API\Repository\RoleService;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use eZ\Publish\API\Repository\Exceptions\NotFoundException;
//use eZ\Publish\Core\Repository\Values\User\Role;
use eZ\Publish\API\Repository\Values\User\Role;

/**
 * Transforms between a Role's ID and a domain specific object.
 */
class RoleTransformer implements DataTransformerInterface
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

    public function transform($value)
    {
        if (null === $value) {
            return null;
        }

        if (!$value instanceof Role) {
            throw new TransformationFailedException('Expected a ' . Role::class . ' object.');
        }

        return $value->id;
    }

    public function reverseTransform($value)
    {
        if (empty($value)) {
            return null;
        }

        try {
            return $this->roleService->loadRole($value);
        } catch (NotFoundException $e) {
            throw new TransformationFailedException('Transformation failed. ' . $e->getMessage(), $e->getCode(), $e);
        }
    }
}
