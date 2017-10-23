<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\DataTransformer;

use eZ\Publish\API\Repository\Exceptions\UnauthorizedException;
use eZ\Publish\API\Repository\SectionService;
use Symfony\Component\Form\DataTransformerInterface;
use eZ\Publish\API\Repository\Values\Content\Section as APISection;
use Symfony\Component\Form\Exception\TransformationFailedException;
use eZ\Publish\API\Repository\Exceptions\NotFoundException;

/**
 * Transforms between a Section's ID and a domain specific object.
 */
class SectionTransformer implements DataTransformerInterface
{
    /** @var SectionService */
    protected $sectionService;

    /**
     * @param SectionService $sectionService
     */
    public function __construct(SectionService $sectionService)
    {
        $this->sectionService = $sectionService;
    }

    /**
     * Transforms a domain specific Section object into a Section identifier.
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

        if (!$value instanceof APISection) {
            throw new TransformationFailedException('Expected a ' . APISection::class . ' object.');
        }

        return $value->id;
    }

    /**
     * Transforms a Section identifier into a domain specific Section object.
     *
     * @param mixed $value
     *
     * @return APISection|null
     *
     * @throws TransformationFailedException
     * @throws UnauthorizedException
     */
    public function reverseTransform($value): ?APISection
    {
        if (empty($value)) {
            return null;
        }

        try {
            return $this->sectionService->loadSection($value);
        } catch (NotFoundException $e) {
            throw new TransformationFailedException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
