<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\DataTransformer;

use eZ\Publish\API\Repository\LocationService;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * Transforms between a Location's ID and a domain specific Location object.
 */
class LocationsTransformer implements DataTransformerInterface
{
    /** @var LocationService */
    protected $locationService;

    /**
     * @param LocationService $locationService
     */
    public function __construct(LocationService $locationService)
    {
        $this->locationService = $locationService;
    }

    /**
     * Transforms a domain specific Location objects into a Location's ID comma separated string.
     *
     * @param mixed $value
     * @return array|mixed|string
     */
    public function transform($value)
    {
        /** TODO add sanity check is array of Location object? */
        if (!is_array($value) || empty($value)) {
            return [];
        }

        return implode(',', array_column($value, 'id'));
    }

    /**
     * Transforms a Location's ID string into a domain specific Location objects.
     *
     * @param mixed $value
     * @return array|null
     * @throws \Symfony\Component\Form\Exception\TransformationFailedException
     */
    public function reverseTransform($value): ?array
    {
        if (empty($value)) {
            return null;
        }

        if (!is_string($value)) {
            throw new TransformationFailedException('Expected a string.');
        }

        $value = explode(',', $value);

        return array_map([$this->locationService, 'loadLocation'], $value);
    }
}
