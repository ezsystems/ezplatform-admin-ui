<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Form\DataTransformer;

use DateInterval;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * Data transformer from PHP DateInterval to array for form inputs.
 */
class DateIntervalToArrayTransformer implements DataTransformerInterface
{
    /**
     * Transforms a date interval into an array of date interval elements.
     *
     * @param \DateInterval $dateInterval date interval
     *
     * @return array date interval elements
     *
     * @throws \Symfony\Component\Form\Exception\TransformationFailedException If the given value is not an instance of DateInterval
     */
    public function transform($dateInterval)
    {
        if ($dateInterval === null) {
            return [
                'year' => '0',
                'month' => '0',
                'day' => '0',
                'hour' => '0',
                'minute' => '0',
                'second' => '0',
            ];
        }

        if (!$dateInterval instanceof DateInterval) {
            throw new TransformationFailedException('Expected a DateInterval.');
        }

        $result = [
            'year' => $dateInterval->format('%y'),
            'month' => $dateInterval->format('%m'),
            'day' => $dateInterval->format('%d'),
            'hour' => $dateInterval->format('%h'),
            'minute' => $dateInterval->format('%i'),
            'second' => $dateInterval->format('%s'),
        ];

        return $result;
    }

    /**
     * Transforms an array of date interval elements into a date interval.
     *
     * @param array $value date interval elements
     *
     * @return \DateInterval date interval
     *
     * @throws \Symfony\Component\Form\Exception\TransformationFailedException if the given value is not an array,
     *                                       or if the value could not be transformed
     */
    public function reverseTransform($value)
    {
        if (null === $value) {
            return;
        }

        if (!is_array($value)) {
            throw new TransformationFailedException('Expected an array.');
        }

        if ('' === implode('', $value)) {
            return;
        }

        // List the fields that are not set as keys in $value
        $emptyFields = array_diff(
            ['year', 'month', 'day', 'hour', 'minute', 'second'],
            array_keys($value)
        );

        if (count($emptyFields) > 0) {
            throw new TransformationFailedException(
                sprintf('Fields "%s" should not be empty', implode('", "', $emptyFields))
            );
        }

        if (isset($value['month']) && !ctype_digit((string)$value['month'])) {
            throw new TransformationFailedException('This month is invalid');
        }

        if (isset($value['day']) && !ctype_digit((string)$value['day'])) {
            throw new TransformationFailedException('This day is invalid');
        }

        if (isset($value['year']) && !ctype_digit((string)$value['year'])) {
            throw new TransformationFailedException('This year is invalid');
        }

        if (!empty($value['month']) && !empty($value['day']) && !empty($value['year']) &&
            false === checkdate($value['month'], $value['day'], $value['year'])) {
            throw new TransformationFailedException('This is an invalid date');
        }

        if (isset($value['hour']) && !ctype_digit((string)$value['hour'])) {
            throw new TransformationFailedException('This hour is invalid');
        }

        if (isset($value['minute']) && !ctype_digit((string)$value['minute'])) {
            throw new TransformationFailedException('This minute is invalid');
        }

        if (isset($value['second']) && !ctype_digit((string)$value['second'])) {
            throw new TransformationFailedException('This second is invalid');
        }

        try {
            $dateInterval = new DateInterval(
                sprintf(
                    'P%sY%sM%sDT%sH%sM%sS',
                    empty($value['year']) ? '0' : $value['year'],
                    empty($value['month']) ? '0' : $value['month'],
                    empty($value['day']) ? '0' : $value['day'],
                    empty($value['hour']) ? '0' : $value['hour'],
                    empty($value['minute']) ? '0' : $value['minute'],
                    empty($value['second']) ? '0' : $value['second']
                )
            );
        } catch (\Exception $e) {
            throw new TransformationFailedException($e->getMessage(), $e->getCode(), $e);
        }

        return $dateInterval;
    }
}
