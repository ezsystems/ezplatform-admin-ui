<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\DataTransformer;

use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\Exceptions\UnauthorizedException;
use eZ\Publish\API\Repository\LocationService;
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
     * @inheritdoc
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Location[]|null
     */
    public function transform($value): ?array
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
        } catch (NotFoundException $e) {
            return null;
        } catch (UnauthorizedException $e) {
            throw new TransformationFailedException($e->getMessage(), $e->getCode(), $e);
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
