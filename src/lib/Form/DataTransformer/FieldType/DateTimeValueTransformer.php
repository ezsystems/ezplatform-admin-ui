<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Form\DataTransformer\FieldType;

use eZ\Publish\Core\FieldType\DateAndTime\Value;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * DataTransformer for DateAndTime\Value.
 */
class DateTimeValueTransformer implements DataTransformerInterface
{
    /**
     * @param mixed $value
     *
     * @return int|null
     *
     * @throws \Symfony\Component\Form\Exception\TransformationFailedException
     */
    public function transform($value)
    {
        if (null === $value) {
            return null;
        }

        if (!$value instanceof Value) {
            throw new TransformationFailedException(
                sprintf('Received %s instead of %s', gettype($value), Value::class)
            );
        }

        if (null === $value->value) {
            return null;
        }

        return $value->value->getTimestamp();
    }

    /**
     * @param mixed $value
     *
     * @return \eZ\Publish\Core\FieldType\DateAndTime\Value|null
     *
     * @throws \Symfony\Component\Form\Exception\TransformationFailedException
     */
    public function reverseTransform($value)
    {
        if (empty($value)) {
            return null;
        }

        if (!is_numeric($value)) {
            throw new TransformationFailedException(
                sprintf('Received %s instead of a numeric value', gettype($value))
            );
        }

        return Value::fromTimestamp($value);
    }
}
