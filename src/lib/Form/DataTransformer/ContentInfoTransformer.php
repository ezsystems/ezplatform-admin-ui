<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\DataTransformer;

use eZ\Publish\API\Repository\ContentService;
use Symfony\Component\Form\DataTransformerInterface;
use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use Symfony\Component\Form\Exception\TransformationFailedException;
use eZ\Publish\API\Repository\Exceptions\NotFoundException;

/**
 * Transforms between a Content's ID and a domain specific ContentInfo object.
 */
class ContentInfoTransformer implements DataTransformerInterface
{
    /** @var ContentService */
    protected $contentService;

    /**
     * @param ContentService $contentService
     */
    public function __construct(ContentService $contentService)
    {
        $this->contentService = $contentService;
    }

    /**
     * Transforms a domain specific ContentInfo object into a Content's ID.
     *
     * @param ContentInfo|null $value
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

        if (!$value instanceof ContentInfo) {
            throw new TransformationFailedException('Expected a ' . ContentInfo::class . ' object.');
        }

        return $value->id;
    }

    /**
     * Transforms a Content's ID integer into a domain specific ContentInfo object.
     *
     * @param mixed $value
     *
     * @return ContentInfo|null
     *
     * @throws NotFoundException
     * @throws TransformationFailedException if the given value is not an integer
     *                                       or if the value can not be transformed
     */
    public function reverseTransform($value): ?ContentInfo
    {
        if (empty($value)) {
            return null;
        }

        if (!is_int($value)) {
            throw new TransformationFailedException('Expected an integer.');
        }

        try {
            return $this->contentService->loadContentInfo($value);
        } catch (NotFoundException $e) {
            throw new TransformationFailedException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
