<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\DataTransformer;

use eZ\Publish\Core\Repository\Values\User\Policy;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * Transforms between a Policy's ID and a domain specific object.
 */
class PolicyTransformer implements DataTransformerInterface
{
    /**
     * Transforms a domain specific Policy object into a Policy string.
     * @param mixed $value
     * @return mixed|null|string
     */
    public function transform($value)
    {
        if (null === $value) {
            return null;
        }

        if (!$value instanceof Policy) {
            throw new TransformationFailedException('Expected a ' . Policy::class . ' object.');
        }

        return implode(':', [$value['id'], $value['module'], $value['function']]);
    }

    /**
     * Transforms a Policy string into a domain specific Policy array.
     */
    public function reverseTransform($value)
    {
        if (null === $value) {
            return null;
        }

        if (!is_string($value)) {
            throw new TransformationFailedException('Expected a string.');
        }

        $parts = explode(':', $value);

        if (count($parts) < 3) {
            throw new TransformationFailedException('Policy string must contain at least 3 parts.');
        }

        return [
            'id' => $parts[0],
            'module' => $parts[1],
            'function' => $parts[2],
        ];
    }
}
