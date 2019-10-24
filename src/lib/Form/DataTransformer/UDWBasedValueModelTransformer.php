<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\DataTransformer;

use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\Exceptions\UnauthorizedException;
use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\Values\Content\Location;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * DataTransformer for UDWBasedMapper.
 *
 * Needed to display the form field correctly and transform it back to an appropriate value object.
 */
class UDWBasedValueModelTransformer implements DataTransformerInterface
{
    /** @var \eZ\Publish\API\Repository\LocationService */
    private $locationService;

    /**
     * @param \eZ\Publish\API\Repository\LocationService $locationService
     */
    public function __construct(LocationService $locationService)
    {
        $this->locationService = $locationService;
    }

    /**
     * {@inheritdoc}
     */
    public function transform($value): ?Location
    {
        if (!is_array($value)) {
            return null;
        }

        try {
            return array_map(function (string $path) {
                return $this->locationService->loadLocation(
                    $this->extractLocationIdFromPath($path)
                );
            }, $value);
        } catch (NotFoundException | UnauthorizedException $e) {
            throw new TransformationFailedException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($value): ?int
    {
        if (!is_array($value)) {
            return null;
        }

        return array_column($value, 'id');
    }

    /**
     * Extracts and returns an item id from a path, e.g. /1/2/58/ => 58.
     *
     * @param string $path
     *
     * @return string|null
     */
    private function extractLocationIdFromPath(string $path): ?string
    {
        $pathParts = explode('/', trim($path, '/'));

        return array_pop($pathParts);
    }
}
