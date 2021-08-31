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
 * Translates timestamp and DataInterval to domain specific timestamp date range.
 */
class DateIntervalTransformer implements DataTransformerInterface
{
    /**
     * @param array|null $value
     *
     * @return array|null
     *
     * @throws \Symfony\Component\Form\Exception\TransformationFailedException
     */
    public function transform($value)
    {
        return null;
    }

    /**
     * @param array|null $value
     *
     * @return array|null
     *
     * @throws \Exception
     * @throws \Symfony\Component\Form\Exception\TransformationFailedException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     */
    public function reverseTransform($value)
    {
        if (null === $value || !is_array($value) || empty($value['date_interval'])) {
            return [];
        }

        if (!array_key_exists('date_interval', $value) || !array_key_exists('end_date', $value)) {
            throw new TransformationFailedException(
                "Invalid data. Value array is missing 'date_interval' and/or 'end_date' keys"
            );
        }

        $date = new \DateTime();

        if ($value['end_date']) {
            $date->setTimestamp($value['end_date']);
        }

        $date->setTime(23, 59, 59);
        $endDate = $date->getTimestamp();
        $interval = new \DateInterval($value['date_interval']);
        $date->sub($interval);
        $date->setTime(00, 00, 00);
        $startDate = $date->getTimestamp();

        return ['start_date' => $startDate, 'end_date' => $endDate];
    }
}
