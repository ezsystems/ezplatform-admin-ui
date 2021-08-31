<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\DataTransformer;

use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\Values\ContentType\ContentType;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * Translates Content Type's identifier to domain specific ContentType object.
 */
class ContentTypeTransformer implements DataTransformerInterface
{
    /** @var \eZ\Publish\API\Repository\ContentTypeService */
    protected $contentTypeService;

    /**
     * @param \eZ\Publish\API\Repository\ContentTypeService $contentTypeService
     */
    public function __construct(ContentTypeService $contentTypeService)
    {
        $this->contentTypeService = $contentTypeService;
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\ContentType\ContentType $value
     *
     * @return string|null
     */
    public function transform($value)
    {
        return null !== $value
            ? $value->identifier
            : null;
    }

    /**
     * @param mixed $value
     *
     * @return \eZ\Publish\API\Repository\Values\ContentType\ContentType|null
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     */
    public function reverseTransform($value)
    {
        return null !== $value && !empty($value)
            ? $this->contentTypeService->loadContentTypeByIdentifier($value)
            : null;
    }
}
