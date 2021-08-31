<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\DataTransformer;

use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\SectionService;
use eZ\Publish\API\Repository\Values\Content\Section as APISection;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * Transforms between a Section's ID and a domain specific object.
 */
final class SectionTransformer implements DataTransformerInterface
{
    /** @var \eZ\Publish\API\Repository\SectionService */
    private $sectionService;

    public function __construct(SectionService $sectionService)
    {
        $this->sectionService = $sectionService;
    }

    public function transform($value): ?int
    {
        if (null === $value) {
            return null;
        }

        if (!$value instanceof APISection) {
            throw new TransformationFailedException('Expected a ' . APISection::class . ' object.');
        }

        return $value->id;
    }

    public function reverseTransform($value): ?APISection
    {
        if (empty($value)) {
            return null;
        }

        if (!is_int($value) && !ctype_digit($value)) {
            throw new TransformationFailedException('Expected a numeric string.');
        }

        try {
            return $this->sectionService->loadSection((int)$value);
        } catch (NotFoundException $e) {
            throw new TransformationFailedException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
