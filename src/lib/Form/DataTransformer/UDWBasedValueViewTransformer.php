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

class UDWBasedValueViewTransformer implements DataTransformerInterface
{
    const DELIMITER = ',';

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
     */
    public function transform($value)
    {
        if (!is_array($value)) {
            return null;
        }

        return implode(self::DELIMITER, array_column($value, 'id'));
    }

    /**
     * @inheritdoc
     */
    public function reverseTransform($value)
    {
        if (!is_string($value) || $value === '') {
            return $value;
        }

        try {
            return array_map([$this->locationService, 'loadLocation'], explode(self::DELIMITER, $value));
        } catch (NotFoundException | UnauthorizedException $e) {
            throw new TransformationFailedException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
