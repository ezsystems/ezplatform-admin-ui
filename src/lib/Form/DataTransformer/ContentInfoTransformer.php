<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\DataTransformer;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * Transforms between a Content's ID and a domain specific ContentInfo object.
 */
final class ContentInfoTransformer implements DataTransformerInterface
{
    /** @var \eZ\Publish\API\Repository\ContentService */
    private $contentService;

    public function __construct(ContentService $contentService)
    {
        $this->contentService = $contentService;
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\ContentInfo|null $value
     */
    public function transform($value): ?int
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
     * @param int|string|null $value
     */
    public function reverseTransform($value): ?ContentInfo
    {
        if (empty($value)) {
            return null;
        }

        if (!is_int($value) && !ctype_digit($value)) {
            throw new TransformationFailedException('Expected a numeric string.');
        }

        try {
            return $this->contentService->loadContentInfo((int)$value);
        } catch (NotFoundException $e) {
            throw new TransformationFailedException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
