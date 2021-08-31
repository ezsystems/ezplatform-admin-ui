<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\DataTransformer;

use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\UserService;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * Transforms between comma separated string of User's ID and an array of domain specific User objects.
 */
class UserCollectionTransformer implements DataTransformerInterface
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
     * @param array|null $value
     *
     * @return string|null
     *
     * @throws \Symfony\Component\Form\Exception\TransformationFailedException
     */
    public function transform($value)
    {
        if (!is_array($value) || empty($value)) {
            return null;
        }

        return implode(',', array_column($value, 'id'));
    }

    /**
     * @param string|null $value
     *
     * @return array
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     * @throws \Symfony\Component\Form\Exception\TransformationFailedException if the given value is not an integer
     *                                                                         or if the value can not be transformed
     */
    public function reverseTransform($value): array
    {
        if (empty($value)) {
            return [];
        }

        if (!is_string($value)) {
            throw new TransformationFailedException('Expected a string.');
        }

        $idArray = explode(',', $value);

        try {
            return array_map([$this->userService, 'loadUser'], $idArray);
        } catch (NotFoundException $e) {
            throw new TransformationFailedException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
