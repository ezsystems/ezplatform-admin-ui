<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\DataTransformer;

use DateTime;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class DateTimePickerTransformer implements DataTransformerInterface
{
    /**
     * Transforms a value from the original representation to a transformed representation.
     *
     * @param mixed $value The value in the original representation
     *
     * @return mixed The value in the transformed representation
     *
     * @throws \Symfony\Component\Form\Exception\TransformationFailedException when the transformation fails
     */
    public function transform($value)
    {
        if (null === $value) {
            return null;
        }

        if (!$value instanceof DateTime) {
            throw new TransformationFailedException(
                sprintf('Found %s instead of %s', gettype($value), DateTime::class)
            );
        }

        return $value->getTimestamp();
    }

    /**
     * Transforms a value from the transformed representation to its original
     * representation.
     *
     * @param mixed $value The value in the transformed representation
     *
     * @return mixed The value in the original representation
     *
     * @throws \Symfony\Component\Form\Exception\TransformationFailedException when the transformation fails
     */
    public function reverseTransform($value)
    {
        if (empty($value)) {
            return null;
        }

        if (!is_numeric($value)) {
            throw new TransformationFailedException(
                sprintf('Found %s instead of a numeric value', gettype($value))
            );
        }

        return DateTime::createFromFormat('U', (string)$value);
    }
}
