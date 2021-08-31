<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\DataTransformer;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\Values\Content\Content;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class ContentTransformer implements DataTransformerInterface
{
    /** @var \eZ\Publish\API\Repository\ContentService */
    protected $contentService;

    /**
     * @param \eZ\Publish\API\Repository\ContentService $contentService
     */
    public function __construct(ContentService $contentService)
    {
        $this->contentService = $contentService;
    }

    /**
     * Transforms a domain specific Content object into a Content's ID.
     *
     * @param \eZ\Publish\API\Repository\Values\Content\Content|null $value
     *
     * @return int|null
     */
    public function transform($value): ?int
    {
        if (null === $value) {
            return null;
        }

        if (!$value instanceof Content) {
            throw new TransformationFailedException('Expected a ' . Content::class . ' object.');
        }

        return $value->id;
    }

    /**
     * Transforms a Content's ID integer into a domain specific Content object.
     *
     * @param string|null $value
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Content|null
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
    public function reverseTransform($value): ?Content
    {
        if (empty($value)) {
            return null;
        }

        if (!filter_var($value, FILTER_VALIDATE_INT)) {
            throw new TransformationFailedException('Expected a numeric string.');
        }

        try {
            return $this->contentService->loadContent((int)$value);
        } catch (NotFoundException $e) {
            throw new TransformationFailedException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
