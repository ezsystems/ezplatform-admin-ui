<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\DataTransformer;

use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\ObjectStateService;
use eZ\Publish\API\Repository\Values\ObjectState\ObjectStateGroup;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * Translates ObjectStateGroup's ID to domain specific ObjectStateGroup object.
 */
class ObjectStateGroupTransformer implements DataTransformerInterface
{
    /** @var \eZ\Publish\API\Repository\ObjectStateService */
    protected $objectStateService;

    /**
     * @param \eZ\Publish\API\Repository\ObjectStateService $objectStateService
     */
    public function __construct(ObjectStateService $objectStateService)
    {
        $this->objectStateService = $objectStateService;
    }

    public function transform($value)
    {
        if (null === $value) {
            return null;
        }

        if (!$value instanceof ObjectStateGroup) {
            throw new TransformationFailedException('Expected a ' . ObjectStateGroup::class . ' object.');
        }

        return $value->id;
    }

    public function reverseTransform($value)
    {
        if (empty($value)) {
            return null;
        }

        try {
            return $this->objectStateService->loadObjectStateGroup((int)$value);
        } catch (NotFoundException $e) {
            throw new TransformationFailedException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
