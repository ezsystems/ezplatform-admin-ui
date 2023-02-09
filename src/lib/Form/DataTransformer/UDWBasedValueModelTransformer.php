<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\DataTransformer;

use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\PermissionResolver;
use eZ\Publish\API\Repository\Repository;
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

    /** @var \eZ\Publish\API\Repository\PermissionResolver */
    private $permissionResolver;

    /** @var \eZ\Publish\API\Repository\Repository */
    private $repository;

    public function __construct(
        LocationService $locationService,
        PermissionResolver $permissionResolver,
        Repository $repository
    ) {
        $this->locationService = $locationService;
        $this->permissionResolver = $permissionResolver;
        $this->repository = $repository;
    }

    /**
     * @inheritdoc
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Location[]|null
     */
    public function transform($value): ?array
    {
        if (!is_array($value)) {
            return null;
        }

        return array_map([$this, 'mapPathToLocation'], $value);
    }

    private function mapPathToLocation(string $path): ?Location
    {
        $locationId = $this->extractLocationIdFromPath($path);

        try {
            // Sudo is necessary as skipping non-accessible Locations
            // will prevent an administrator from editing policies
            return $this->permissionResolver->sudo(
                function (Repository $repository) use ($locationId): Location {
                    return $this->locationService->loadLocation($locationId);
                },
                $this->repository
            );
        } catch (NotFoundException $e) {
            return null;
        }
    }

    /**
     * @inheritdoc
     *
     * @return int[]|null
     */
    public function reverseTransform($value): ?array
    {
        if (!is_array($value)) {
            return null;
        }

        return array_column($value, 'id');
    }

    /**
     * Extracts and returns an item id from a path, e.g. /1/2/58/ => 58.
     */
    private function extractLocationIdFromPath(string $path): int
    {
        $pathParts = explode('/', trim($path, '/'));

        $locationId = array_pop($pathParts);
        if ($locationId === null) {
            throw new TransformationFailedException("Path '{$path}' does not contain Location ID");
        }

        return (int)$locationId;
    }
}
