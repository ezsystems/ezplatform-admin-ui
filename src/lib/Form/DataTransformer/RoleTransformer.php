<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\DataTransformer;

use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\RoleService;
use eZ\Publish\API\Repository\Values\User\Role as APIRole;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * Transforms between a Role's ID and a domain specific object.
 */
class RoleTransformer implements DataTransformerInterface
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
     * Transforms a domain specific Role object into a Role identifier.
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

        if (!$value instanceof APIRole) {
            throw new TransformationFailedException('Expected a ' . APIRole::class . ' object.');
        }

        return $value->id;
    }

    /**
     * Transforms a Role identifier into a domain specific Role object.
     *
     * @param mixed $value
     *
     * @return \eZ\Publish\API\Repository\Values\User\Role|null
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     * @throws \Symfony\Component\Form\Exception\TransformationFailedException
     */
    public function reverseTransform($value): ?APIRole
    {
        if (empty($value)) {
            return null;
        }

        if (!ctype_digit($value)) {
            throw new TransformationFailedException('Expected a numeric string.');
        }

        try {
            return $this->roleService->loadRole((int)$value);
        } catch (NotFoundException $e) {
            throw new TransformationFailedException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
