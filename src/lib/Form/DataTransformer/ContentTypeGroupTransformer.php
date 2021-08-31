<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\DataTransformer;

use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\Values\ContentType\ContentTypeGroup;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * Translates ContentTypeGroup's ID to domain specific ContentTypeGroup object.
 */
final class ContentTypeGroupTransformer implements DataTransformerInterface
{
    /** @var \eZ\Publish\API\Repository\ContentTypeService */
    private $contentTypeService;

    public function __construct(ContentTypeService $contentTypeService)
    {
        $this->contentTypeService = $contentTypeService;
    }

    public function transform($value): ?int
    {
        if (null === $value) {
            return null;
        }

        if (!$value instanceof ContentTypeGroup) {
            throw new TransformationFailedException('Expected a ' . ContentTypeGroup::class . ' object.');
        }

        return $value->id;
    }

    public function reverseTransform($value): ?ContentTypeGroup
    {
        if (empty($value)) {
            return null;
        }

        if (!is_int($value) && !ctype_digit($value)) {
            throw new TransformationFailedException('Expected a numeric string.');
        }

        try {
            return $this->contentTypeService->loadContentTypeGroup((int)$value);
        } catch (NotFoundException $e) {
            throw new TransformationFailedException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
