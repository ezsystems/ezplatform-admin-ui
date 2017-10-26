<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\DataTransformer;

use eZ\Publish\API\Repository\Exceptions\UnauthorizedException;
use eZ\Publish\API\Repository\RoleService;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\Values\User\Role as APIRole;

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

    /**
     * Transforms a domain specific Role object into a Role identifier.
     *
     * @param mixed $value
     *
     * @return mixed|null
     *
     * @throws TransformationFailedException
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
     * @return APIRole|null
     *
     * @throws UnauthorizedException
     * @throws TransformationFailedException
     */
    public function reverseTransform($value): ?APIRole
    {
        if (empty($value)) {
            return null;
        }

        try {
            return $this->roleService->loadRole($value);
        } catch (NotFoundException $e) {
            throw new TransformationFailedException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
