<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\DataTransformer;

use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\UserService;
use eZ\Publish\API\Repository\Values\User\User;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * Transforms between a User's ID and a domain specific User object.
 */
class UserTransformer implements DataTransformerInterface
{
    /** @var \eZ\Publish\API\Repository\UserService */
    protected $userService;

    /**
     * @param \eZ\Publish\API\Repository\UserService $userService
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Transforms a domain specific User object into a Users's ID.
     *
     * @param \eZ\Publish\API\Repository\Values\User\User|null $value
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

        if (!$value instanceof User) {
            throw new TransformationFailedException('Expected a ' . User::class . ' object.');
        }

        return $value->id;
    }

    /**
     * Transforms a Users's ID integer into a domain specific User object.
     *
     * @param mixed|null $value
     *
     * @return \eZ\Publish\API\Repository\Values\User\User|null
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     * @throws \Symfony\Component\Form\Exception\TransformationFailedException if the given value is not an integer
     *                                                                         or if the value can not be transformed
     */
    public function reverseTransform($value): ?User
    {
        if (empty($value)) {
            return null;
        }

        if (!is_numeric($value)) {
            throw new TransformationFailedException('Expected a numeric string.');
        }

        try {
            return $this->userService->loadUser((int)$value);
        } catch (NotFoundException $e) {
            throw new TransformationFailedException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
