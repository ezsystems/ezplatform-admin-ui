<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * Transforms between a Policy's ID and a domain specific object.
 */
class PolicyTransformer implements DataTransformerInterface
{
    /**
     * Transforms a domain specific Policy object into a Policy string.
     *
     * @param mixed $value
     *
     * @return string|null
     *
     * @throws \Symfony\Component\Form\Exception\TransformationFailedException
     */
    public function transform($value): ?string
    {
        if (null === $value) {
            return null;
        }

        if (!is_array($value) || array_diff(['id', 'module', 'function'], array_keys($value))) {
            throw new TransformationFailedException('Expected a valid array of data.');
        }

        return implode(':', [$value['id'], $value['module'], $value['function']]);
    }

    /**
     * Transforms a Policy string into a domain specific Policy array.
     *
     * @param string|null $value
     *
     * @return array|null
     *
     * @throws \Symfony\Component\Form\Exception\TransformationFailedException
     */
    public function reverseTransform($value): ?array
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
