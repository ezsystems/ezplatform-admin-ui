<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\DataTransformer;

use eZ\Publish\API\Repository\Exceptions\UnauthorizedException;
use eZ\Publish\API\Repository\LocationService;
use Symfony\Component\Form\DataTransformerInterface;
use eZ\Publish\API\Repository\Values\Content\Location as APILocation;
use Symfony\Component\Form\Exception\TransformationFailedException;
use eZ\Publish\API\Repository\Exceptions\NotFoundException;

/**
 * Transforms between a Location's ID and a domain specific object.
 */
class LocationTransformer implements DataTransformerInterface
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
     * Transforms a domain specific Location object into a Location's identifier.
     *
     * @param mixed $value
     *
     * @return mixed|null
     *
     * @throws TransformationFailedException
     */
    public function transform($value)
    {
        if (null === $value) {
            return null;
        }

        if (!$value instanceof APILocation) {
            throw new TransformationFailedException('Expected a ' . APILocation::class . ' object.');
        }

        return $value->id;
    }

    /**
     * Transforms a Location's ID into a domain specific Location object.
     *
     * @param mixed $value
     *
     * @return APILocation|null
     *
     * @throws TransformationFailedException
     * @throws UnauthorizedException
     */
    public function reverseTransform($value): ?APILocation
    {
        if (empty($value)) {
            return null;
        }

        try {
            return $this->locationService->loadLocation($value);
        } catch (NotFoundException $e) {
            throw new TransformationFailedException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
