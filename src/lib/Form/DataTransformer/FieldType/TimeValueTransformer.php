<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace EzSystems\EzPlatformAdminUi\Form\DataTransformer\FieldType;

use eZ\Publish\API\Repository\Exceptions\InvalidArgumentException;
use eZ\Publish\Core\FieldType\Time\Value;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * DataTransformer for Time\Value.
 */
class TimeValueTransformer implements DataTransformerInterface
{
    /**
     * @param mixed|Value $value
     *
     * @return int|null
     *
     * @throws TransformationFailedException
     */
    public function transform($value)
    {
        if (null === $value->time) {
            return null;
        }

        if (!$value instanceof Value) {
            throw new TransformationFailedException(
                sprintf('Expected a %s', Value::class)
            );
        }

        return $value->time;
    }

    /**
     * @param int|mixed $value
     *
     * @return Value|null
     *
     * @throws InvalidArgumentException
     * @throws TransformationFailedException
     */
    public function reverseTransform($value)
    {
        if (null === $value || $value === '') {
            return null;
        }

        if (!is_numeric($value)) {
            throw new TransformationFailedException(
                sprintf('Expected a numeric, got %s instead', gettype($value))
            );
        }

        return new Value($value);
    }
}
